CREATE TABLE `cardcategories` (
  `categoryid` int(11) NOT NULL,
  `categoryname` varchar(100) DEFAULT NULL,
  `parentcategoryid` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `cards` (
  `cardid` int(11) NOT NULL,
  `cardquestion` varchar(255) DEFAULT NULL,
  `cardanswer` varchar(100) DEFAULT NULL,
  `cardsetid` int(11) DEFAULT NULL,
  `cardorder` int(11) DEFAULT '0',
  `manualanswer` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `cardset` (
  `cardsetid` int(11) NOT NULL,
  `setname` varchar(100) DEFAULT NULL,
  `categoryid` int(11) DEFAULT NULL,
  `originalcardsetid` int(11) DEFAULT NULL,
  `setorder` int(11) DEFAULT '0',
  `userid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `cardsetusers` (
  `userid` int(11) NOT NULL,
  `cardsetid` int(11) NOT NULL,
  `setorder` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `errorlog` (
  `errorid` int(11) NOT NULL,
  `errormsg` varchar(200) DEFAULT NULL,
  `moreinfo` varchar(1000) DEFAULT NULL,
  `filename` varchar(200) DEFAULT NULL,
  `functionname` varchar(50) DEFAULT NULL,
  `linenum` int(11) DEFAULT NULL,
  `errordate` datetime DEFAULT NULL,
  `sessioninfo` varchar(1000) DEFAULT NULL,
  `parentid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `sessionresults` (
  `sessionid` int(11) NOT NULL,
  `cardid` int(11) NOT NULL,
  `cardresult` int(11) DEFAULT '0',
  `qaswapped` int(11) NOT NULL DEFAULT '0',
  `givenanswer` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `studysession` (
  `sessionid` int(11) NOT NULL,
  `sessionstart` datetime DEFAULT NULL,
  `sessionend` datetime DEFAULT NULL,
  `cardsetid` int(11) NOT NULL,
  `userid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tempsession` (
  `sessionid` int(11) NOT NULL,
  `sessionstart` datetime DEFAULT NULL,
  `sessionend` datetime DEFAULT NULL,
  `lastaction` datetime DEFAULT NULL,
  `userid` int(11) NOT NULL,
  `cardsetid` int(11) DEFAULT NULL,
  `categoryid` int(11) DEFAULT NULL,
  `sessiontype` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tempsessioncards` (
  `sessionid` int(11) NOT NULL,
  `cardid` int(11) NOT NULL,
  `qaswapped` int(11) NOT NULL DEFAULT '0',
  `cardresult` int(11) NOT NULL DEFAULT '0',
  `givenanswer` varchar(100) DEFAULT NULL,
  `cardcomplete` int(11) NOT NULL DEFAULT '0',
  `historyserial` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `userdetails` (
  `userid` int(11) NOT NULL,
  `email` varchar(200) DEFAULT NULL,
  `passsword` varchar(255) DEFAULT NULL,
  `parentuserid` int(11) DEFAULT '0',
  `firstname` varchar(200) DEFAULT NULL,
  `lastname` varchar(200) DEFAULT NULL,
  `payid` varchar(64) DEFAULT NULL,
  `packageid` int(11) DEFAULT '0',
  `packagestatus` int(11) DEFAULT '0',
  `packageexpires` date DEFAULT NULL,
  `subscriptionid` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `cardcategories` ADD PRIMARY KEY (`categoryid`);
ALTER TABLE `cards` ADD PRIMARY KEY (`cardid`);
ALTER TABLE `cardset` ADD PRIMARY KEY (`cardsetid`);
ALTER TABLE `errorlog` ADD PRIMARY KEY (`errorid`);
ALTER TABLE `studysession` ADD PRIMARY KEY (`sessionid`);
ALTER TABLE `tempsession` ADD PRIMARY KEY (`sessionid`);
ALTER TABLE `userdetails` ADD PRIMARY KEY (`userid`);
COMMIT;