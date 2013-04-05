# coding:utf-8
import urllib2, urllib, os

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
            

class Updater(object):
    url = "http://download.updater.y0.pl/index.php"
    
    def __init__(self, project = "", passw = ""):
        try:
            self.name = open("info.info").readlines()[0]
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
            data = urllib.urlencode(data)
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
            for x in self.get_files():
                try:
                    ff = self.get_file(x)
                    open(ff[1], "wb").write(ff[2])
                except:
                    print "Warrning! Couldn't update file", x[0] , x[1]
            open("info.info", "w").write(self.name + "\n" + vr)
            print "Updated", self.name, "to version", vr, "!"
        else:
            print "No need for update."
            
    
    
        