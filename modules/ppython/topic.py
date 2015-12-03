# __author__ = 'Houzhuo'
# -*- coding: utf-8 -*-
import sys
reload(sys)
sys.setdefaultencoding('utf-8')
sys.path.append('../')

import jieba
import jieba.analyse

from optparse import OptionParser

def topic():


    # USAGE = "usage:    python extract_tags.py [file name] -k [top k]"
    #
    # parser = OptionParser(USAGE)
    # parser.add_option("-k", dest="topK")
    # opt, args = parser.parse_args()
    # if len(args) < 1:
    #     print(USAGE)
    #     sys.exit(1)
    #
    # file_name = args[0]
    #
    # if opt.topK is None:
    #     topK = 10
    # else:
    #     topK = int(opt.topK)

    filename = "topic.text"
    list = []
    f = file(filename, 'rb')

    content = f.read()
    f.close()
    print content

    #content = open(filename, 'rb').read()

    tags = jieba.analyse.extract_tags(content, 5)

    print(",".join(tags))
    print tags
    print type(tags)


    writefile = file(filename, 'wb')
    for i in tags:
        print i
        list.append(i)
        writefile.write(i)
        writefile.write("\r\n")
        
    print list


    writefile.close()
    return True;

if __name__ == '__main__':
    topic()

