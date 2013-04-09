# coding:utf-8

from distutils.core import setup
import py2exe
import sys, os, shutil
import operator


if __name__ == "__main__":
    if operator.lt(len(sys.argv), 2):
        sys.argv.append('py2exe')
    if os.path.isdir("dist"): #Erase previous destination dir
            print "Removing", os.path.abspath("dist")
            shutil.rmtree("dist")
    
    options = {"optimize" : 2,
               "compressed" : 1,
               "bundle_files" : 1,
               }        
    setup(console = ['main.py'], zipfile = "update.dll", options = {"py2exe" : options})
    
    
    if os.path.isdir('build'): #Clean up build dir
        print "Removing", os.path.abspath("build")
        shutil.rmtree('build')
    print "Renaming main.exe"
    os.rename("dist/main.exe", "dist/Update.exe")
    print "Done!"
