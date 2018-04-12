import numpy as np
import cv2
import Person
import time
import threading
from ws4py.client.threadedclient import WebSocketClient
import json

cnt_up = 0
flag = False

cap = cv2.VideoCapture("sample-video.avi")
cap.set(3, 320)  # Width
cap.set(4, 240)  # Height

w = cap.get(3)
h = cap.get(4)
frameArea = h * w
areaTH = frameArea / 250
print 'Area Threshold', areaTH

# Position of the Lines
line_up = int(2.5 * (h / 5))
line_down = int(3 * (h / 5))

up_limit = int(2 * (h / 5))
down_limit = int(3.5 * (h / 5))

fgbg = cv2.createBackgroundSubtractorMOG2(detectShadows=True)

kernelOp = np.ones((3, 3), np.uint8)
kernelCl = np.ones((11, 11), np.uint8)

# Variables
font = cv2.FONT_HERSHEY_SIMPLEX
persons = []
pid = 1


def dataSender():
    global cnt_up
    global ws

    if flag:
        threading.Timer(2.0, dataSender).start()

        if cnt_up > 0:
            # Send Data to the server
            print cnt_up
            ws.send_message({
                "action": "add",
                "amount": cnt_up,
                "datetime": time.strftime("%Y-%m-%d %H:00:00")
            })

            cnt_up = 0
    else:
        ws.close()


class DummyClient(WebSocketClient):
    def opened(self):
        data = json.dumps({
            "action": "setname",
            "username": "hardwareDevice"
        })
        self.send(data)
        print "Connecting to the Server..."


    def closed(self, code, reason=None):
        print "Closed down", code, reason


    def received_message(self, m):
        data = json.loads(str(m))

        if data["action"] == "setname":
            if data["success"]:
                print "Device Successfully Connected"
                dataSender()
                capture()
            else:
                print "There is a problem connecting to the Server..."

    def send_message(self, msg):
        msg = json.dumps(msg)
        self.send(msg)

def capture():
    # Initialize all of the global variables
    global cap
    global fgbg
    global kernelCl
    global kernelCl
    global up_limit
    global down_limit
    global line_up
    global line_down
    global cnt_up
    global pid
    global persons
    global font
    global flag

    # Real-time Capturing
    while cap.isOpened():
        flag = True

        ret, frame = cap.read()
        fgmask2 = fgbg.apply(frame)

        try:
            ret, imBin2 = cv2.threshold(fgmask2, 200, 255, cv2.THRESH_BINARY)

            # Opening
            mask2 = cv2.morphologyEx(imBin2, cv2.MORPH_OPEN, kernelOp)
            # Closing

            mask2 = cv2.morphologyEx(mask2, cv2.MORPH_CLOSE, kernelCl)
        except:
            break

        _, contours0, hierarchy = cv2.findContours(mask2, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)
        for cnt in contours0:
            area = cv2.contourArea(cnt)

            if area > areaTH:
                M = cv2.moments(cnt)
                cx = int(M['m10'] / M['m00'])
                cy = int(M['m01'] / M['m00'])
                x, y, w, h = cv2.boundingRect(cnt)

                new = True
                if cy in range(up_limit, down_limit):
                    for i in persons:

                        if abs(cx - i.getX()) <= w and abs(cy - i.getY()) <= h:
                            new = False
                            i.updateCoords(cx, cy)

                            # Check if a person is going up
                            if i.going_UP(line_down, line_up):
                                # Add to count
                                cnt_up += 1
                            break

                        if i.getState() == '1' and i.getDir() == 'up' and i.getY() < up_limit:
                            i.setDone()

                        if i.timedOut():
                            index = persons.index(i)
                            persons.pop(index)
                            del i

                    if new:
                        p = Person.MyPerson(pid, cx, cy)
                        persons.append(p)
                        pid += 1

                cv2.rectangle(frame, (x, y), (x + w, y + h), (0, 255, 0), 2)

        cv2.imshow('Frame1', frame)
        cv2.imshow('Frame', mask2)

        # Press ESC to terminate the program
        k = cv2.waitKey(1) & 0xff
        if k == 27:
            break

    cap.release()
    cv2.destroyAllWindows()
    flag = False


if __name__ == '__main__':
    try:
        ws = DummyClient('ws://127.0.0.1:2000/')
        ws.connect()
        ws.run_forever()

    except KeyboardInterrupt:
        ws.close()
