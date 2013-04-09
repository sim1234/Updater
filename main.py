# coding:utf-8

import os, sys
#from update import Updater, provide_folder, compareV, TMP
from update import *


def interact():
    print "Commands: exit, update [x], shell, cd"
    r = raw_input(">")
    while not r in ("quit", "exit"):
        if r.startswith("update"):
            rr = r.split(" ", 1)
            if len(rr) == 2:
                try:
                    os.remove("info.info")
                except:
                    pass
            else:
                rr.append("")
            u = Updater(rr[1])
            u.update(1)
        
        elif r.startswith("shell"):
            u = Updater()
            rr = 1
            while rr:
                try:
                    exec(raw_input(">>>"))
                except Exception as e:
                    print e
        elif r.startswith("cd"):
            try:
                os.chdir(r.split(" ", 1)[1])
            except Exception as e:
                    print e
        
        else:
            print "Wut? o_O"
        
        r = raw_input(">")
        

def main():
    if raw_input("To interact write 'i'!") == "i":
        interact()
    else:
        print "Imma update yo sowtware!"
        try:
            u = Updater()
            u.update()
        except Exception as e:
            print "Error:", e
        raw_input("Press Enter to exit.")


if __name__ == "__main__":
    main()