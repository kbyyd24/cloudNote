#数据库结构：   
> code为用户编号   

| database   | tables           |
|:-----------:|:----------------:|
| cloudNote | users_data       |
|            | users_infomation |

#数据表结构:   
###users_data:
| properties | descriptions |
|:----------:|:------------:|
| id | 用户编号 |
| objectKey | 存储在 OSS 上的文件名，建议格式：用户编号/objectName |
| contentLength | object的大小 |
| audioTime | 音频文件长度 |
| remark | object的备注，可以为空 |
| contentType | object的文件类型 |
| filename | 显示给用户的文件名，默认为创建时的 GMT+8 时间 |
| Time | object创建的时间，GMT+8 格式 |

###users_infomation
| propreties | descriptions |
|:----------:|:------------:|
| name | 用户名 |
| id | 用户id，不可更改，自增 |
| password | 密码 |


#各函数参数：

| functionName | parameters | descriptions |
|:----------:|:----------:|:------------:|
| cloud | code | 用户ID |
|  | page | 列表页数 |
|  | number | 每页object个数，默认为10，可选 |

####useage:

利用该接口查看用户已有object信息

######返回xml形式：
```xml
<?xml version='1.0' encoding='UTF-8'?>
<root>
  <data>
    <filename></filename>
    <length></length>
    <remark></remark>
  </data>
  <data>
   ...
  </data>
</root>
```

---
| functionName | parameters | descriptions |
|:------------:|:----------:|:------------:|
| delete | code | 用户ID |
|  | filename | 所要删除的文件名 |

####userage:

利用该接口删除object，目前只能删除数据库记录，不会删除object 

######返回xml形式：
```xml
<?xml version='1.0' encoding='UTF-8'?>
<root>
  <result></result>
</root>
```

---
| functionName | parameters | descriptions |
|:------------:|:----------:|:------------:|
| download | filename | object名称 |
| | fileLength | 本地文件大小，用户断点续传 |
| | code | 用户ID |

####useage:

下载object，支持断点下载。返回存储可用headers内容的xml，解析并写入headers，访问OSS

######返回xml形式：
```xml
<?xml version='1.0' encoding='UTF-8'?>
<root>
  <authorization></authorization>
  <bucket></bucket>
  <date></date>
  <rang></rang>
</root>
```

---
| functionName | parameters | descriptions |
|:------------:|:----------:|:------------:|
| log | filename | object名称 |
| | code | 用户ID |
| | contentLength | object的大小 |
| | contentType | object的文件类型 |
| | objectKey | object在OSS上的位置 |
| | audioTime | 音频文件的长度 |
| | Time | object创建的时间，GMT+8 格式 |
| | remark | 对object的描述、备注，可选 |

####useage:

把上传记录存入数据库

######返回xml形式：
```xml
<?xml version='1.0' encoding='UTF-8'?>
<root>
  <result></result>
</root>
```

---
| functionName | parameters | descriptions |
|:------------:|:----------:|:------------:|
| upload | contentType | object的文件类型 |
| | objectKey | object在OSS上的位置 |

####useage:

上传文件接口，返回存储可用headers信息的xml，解析并写入headers访问OSS

######返回xml形式：
```xml
<?xml version='1.0' encoding='UTF-8'?>
<root>
  <authorization></authorization>
  <contentType></contentType>
  <bucket></bucket>
  <date></date>
</root>
```

---
| functionName | parameters | descriptions |
|:------------:|:----------:|:------------:|
| register | name | 用户名 |
| | password | 密码 |

####useage:

注册接口，传入用户名和密码，成功返回结果和id，失败id=0

######返回xml形式：
```xml
<?xml version='1.0' encoding='UTF-8'?>
<root>
  <result></result>
  <id></id>
</root>
```

---
| functionName | parameters | descriptions |
|:------------:|:----------:|:------------:|
| register | name | 用户名 |
| | password | 密码 |

####useage:

登陆接口，传入用户名和密码，成功返回结果和id，失败id=0

######返回xml形式：
```xml
<?xml version='1.0' encoding='UTF-8'?>
<root>
  <result></result>
  <id></id>
</root>
```
