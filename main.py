# coding:utf-8
from update import Updater
def main():
    u = Updater("lol")
    print u.newp("elo", "lol", "1.0")
    print u.newf("README", "data", "elo", "lol")
    print u.newf("encode.py", "data", "elo", "lol")
    print u.sumf("encode.py", "data", "elo", "lol")
    #u.update(1)
    print u.delf("encode.py", "data", "elo", "lol")
    print u.delp("elo", "lol")

if __name__ == "__main__":
    main()