# coding:utf-8
import urllib2, urllib, os, sys, time, shutil
from encode import multipart_encode

def get_data(s, delimiter):
    delimiter = str(delimiter)
    r = []
    while (1):
        try:
            i1 = s.index("<" + delimiter + ">") + len(delimiter) + 2
            i2 = s.index("</" + delimiter + ">")
            r.append(s[i1:i2])
            s = s[i2 + len(delimiter) + 3:]
        except:
            return r

def compareV(v1, v2):
    try:
        v1 = v1.split(".")
        v2 = v2.split(".")
    except:
        v1 = [v1]
        v2 = [v2]
    v1 = map(int, v1)
    v2 = map(int, v2)
    x = 0
    while x < len(v1) or x < len(v2):
        try:
            if v1[x] < v2[x]:
                return 1
            if v1[x] > v2[x]:
                return -1
            x += 1    
        except:
            return 1
    return 0

def provide_folder(path, iffile = 0):
    p = path.split(os.path.sep)
    if iffile:
        p = p[:-1]
    ppp = ""
    for x in p:
        ppp = os.path.join(ppp, x)
        if not os.path.exists(ppp):
            os.mkdir(ppp)
            

class TMP(object):
    def __init__(self):
        self.d = []
        self.f = []
    
    def get_folder(self, name = "", empty = 1):
        if not empty:
            if len(self.d):
                return self.d[0]
            else:
                return self.get_folder(name, 1)
        else:
            name = os.path.abspath(str(name) or "tmp")
            p = name
            x = 0
            while os.path.exists(p):
                p = name + str(x)
            os.mkdir(p)
            self.d.append(p)
            return os.path.relpath(p, os.path.abspath(""))
    
    def get_file(self, name = ""):
        if not len(self.d):
            self.get_folder()

        if name:
            for d in self.d:
                p = os.path.join(d, name)
                if not os.path.exists(p):
                    open(p, "wb").close()
                    self.f.append(p)
                    return p
            p = os.path.join(self.get_folder(empty=1), name)
            open(p, "wb").close()
            self.f.append(p)
            return p
        
        p = os.path.join(self.d[0], "tmp.tmp")
        x = 0
        while not os.path.exists(p):
            p = os.path.join(self.d[0], "tmp" + str(x) + ".tmp")
            x += 1
        open(p, "wb").close()
        self.f.append(p)
        return p
    
    def __del__(self):
        for f in self.f:
            if os.path.exists(f):
                os.remove(f)
        for d in self.d:
            shutil.rmtree(d, 1)
         
            
        
    


class Updater(object):
    url = "http://download.updater.y0.pl/index.php"
    url2 = "http://edit.updater.y0.pl/index.php"
    
    def __init__(self, project = "", passw = ""):
        try:
            self.name = open("info.info").readlines()[0][:-1]
        except:
            self.name = project
        self.passw = passw
        #self.get_r_version()
        #self.get_l_version()
        
    def get_r_version(self):
        r = self.get_url(self.url)
        r = get_data(r, "content")[0]
        #self.version = self.id = None
        for x in get_data(r, "project"):
            if get_data(x, "name")[0] == self.name:
                #self.version = get_data(x, "version")[0]
                return get_data(x, "version")[0]
                #self.id = get_data(x, "id")[0]
        #return self.id, self.version
        return "0"
        
    def get_l_version(self):
        try:
            return open("info.info").readlines()[1]
            #from main import __version__ as vr
            #return vr
        except:
            return "0"
            
        
    def get_url(self, url = "", data = None, headers = None):
        if not url:
            url = self.url
        if not headers:
            headers = {}
        rq = None
        if data:
            #data = urllib.urlencode(data)
            data, headerss = multipart_encode(data)
            headers.update(headerss)
            rq = urllib2.Request(url, data, headers)
        else:
            rq = urllib2.Request(url, headers = headers)
        r = urllib2.urlopen(rq).read()
        return r
    
    def get_files(self):
        r = self.get_url(self.url + "?project=" + self.name)
        r = get_data(r, "content")[0]
        f = []
        for x in get_data(r, "file"):
            f.append([get_data(x, "id")[0], get_data(x, "name")[0], get_data(x, "path")[0]])
        return f          
        
    def get_file(self, filee):
        r = self.get_url(self.url + "?project=" + self.name + "&file=" + str(filee[0]))
        return [filee[0], os.path.join(filee[2], filee[1]), r]
    
    def update(self, f = 0):
        vr = self.get_r_version()
        if f or compareV(self.get_l_version(), vr) == 1:
            for i in os.listdir(os.path.abspath("")):
                if os.path.isdir(i):
                    shutil.rmtree(i, 1)
                else:
                    try:
                        os.remove(i)
                    except:
                        pass
            
            for x in self.get_files():
                try:
                    ff = self.get_file(x)
                    #print ff[1], os.path.split(ff[1])
                    provide_folder(ff[1], 1)
                    open(ff[1], "wb").write(ff[2])
                except Exception as e:
                    print "Warrning! Couldn't update file", x[0] , x[1], e
            open("info.info", "w").write(self.name + "\n" + vr)
            print "Updated", self.name, "to version", vr, "!"
        else:
            print "No need for update."
            
            
    def newp(self, name, passw, version):
        data = {"name" : str(name),
                "pass" : str(passw),
                "v" : str(version)}
        r = self.get_url(self.url2 + "?c=newp", data)
        return get_data(r, "content")[0][2:-1]
        
    def newf(self, filee, path, project, passw):
        data = {"path" : str(path),
                "pass" : str(passw),
                "project" : str(project),
                "file" : open(filee, "rb")}
        r = self.get_url(self.url2 + "?c=newf", data)
        return get_data(r, "content")[0][2:-1]
    
    def delp(self, name, passw):
        data = {"name" : str(name),
                "pass": str(passw)}     
        r = self.get_url(self.url2 + "?c=delp", data)
        return get_data(r, "content")[0][2:-1]
    
    
    def delf(self, name, path, project, passw):
        data = {"path" : str(path),
                "pass" : str(passw),
                "project" : str(project),
                "name" : str(name)}
        r = self.get_url(self.url2 + "?c=delf", data)
        return get_data(r, "content")[0][2:-1]
    
    def sumf(self, filee, path, project, passw):
        data = {"path" : str(path),
                "pass" : str(passw),
                "project" : str(project),
                "file" : open(filee, "rb")}
        r = self.get_url(self.url2 + "?c=sumf", data)
        return get_data(r, "content")[0][2:-1]
    
    def sums(self, content, name, path, project, passw):
        data = {"path" : str(path),
                "pass" : str(passw),
                "project" : str(project),
                "content" : str(content),
                "name" : str(name)}
        r = self.get_url(self.url2 + "?c=sums", data)
        return get_data(r, "content")[0][2:-1]
    
    def push(self, version):
        print self.delp(self.name, self.passw)
        print self.newp(self.name, self.passw, version)
        tmp = TMP()
        #tmp.get_folder("../pushtmp")
        for root, dirs, files in os.walk(os.path.abspath("")):
            rr = os.path.relpath(root, os.path.abspath(""))
            if rr == "" or rr == "." or not ("\\." in rr or rr.startswith(".") or "\\~" in rr or rr.startswith("~")):
                for f in files:
                    if (not f.startswith(".")) and (not f == "info.info"):
                        p = os.path.join(rr, f)
                        print "Dodawanie '" + str(p) + "':"
                        d, fn = os.path.split(p)
                        #print self.newf(p, d, self.name, self.passw)
                        wf = open(p, "rb")
                        pp = tmp.get_file(fn)
                        fff = open(pp, "wb")
                        fff.write(wf.read(2**16))
                        fff.flush()
                        fff.close()
                        print self.newf(pp, d, self.name, self.passw)
                        chunk = wf.read(2**16)
                        while chunk:
                            print self.sums(chunk, fn, d, self.name, self.passw)
                            chunk = wf.read(2**16)
                            




 
if __name__ == "__main__":
    u = Updater()
    u.update(u.get_l_version() == "0")
    #raw_input("Enter by zakończyć...")
    time.sleep(3)
    