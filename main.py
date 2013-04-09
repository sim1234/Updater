# coding:utf-8

import os
from update import Updater, provide_folder


def main():
    provide_folder("tmp")
    os.chdir("tmp")
    #os.chdir("../Achtung")
    #u = Updater("Achtung", "jonelama")
    u = Updater("Achtung", "")
    #print u.newp("elo", "lol", "1.0")
    #print u.newf("README", "data", "elo", "lol")
    #print u.newf("encode.py", "data", "elo", "lol")
    #print u.sumf("README", "data", "elo", "lol")
    #u.update(1)
    #print u.delf("encode.py", "data", "elo", "lol")
    #print u.delp("elo", "lol")
    #u.push("1.1")
    u.update()
    
    os.chdir("..")

if __name__ == "__main__":
    main()