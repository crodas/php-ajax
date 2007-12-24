CREATE TABLE `chat` (
  `postid` int(11) NOT NULL auto_increment,
  `userid` varchar(32) NOT NULL,
  `postip` varchar(20)  NOT NULL,
  `postcontent` varchar(250)  NOT NULL,
  PRIMARY KEY  (`postid`)
) ENGINE=MEMORY ;
