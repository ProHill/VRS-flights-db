CREATE TABLE IF NOT EXISTS `flightstable` (
  `ID` int(11) NOT NULL,
  `ModeS` varchar(6) CHARACTER SET utf8 NOT NULL,
  `Country` varchar(24) CHARACTER SET utf8 DEFAULT NULL,
  `Registration` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `Operator` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `Callsign` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `ModelCode` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `AircraftModel` varchar(40) CHARACTER SET utf8 DEFAULT NULL,
  `OperatorCode` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `FirstSeen` datetime NOT NULL,
  `FirstLatitude` double DEFAULT NULL,
  `FirstLongitude` double DEFAULT NULL,
  `FirstAltitude` int(11) DEFAULT NULL,
  `LastSeen` datetime NOT NULL,
  `LastLatitude` double DEFAULT NULL,
  `LastLongitude` double DEFAULT NULL,
  `LastAltitude` int(11) DEFAULT NULL,
  `NumPositionReports` int(11) DEFAULT NULL,
  `FromICAO` char(4) CHARACTER SET utf8 DEFAULT NULL,
  `FromIATA` char(3) CHARACTER SET utf8 DEFAULT NULL,
  `FromName` varchar(80) CHARACTER SET utf8 DEFAULT NULL,
  `FromLat` double DEFAULT NULL,
  `FromLong` double DEFAULT NULL,
  `FromLocation` varchar(80) CHARACTER SET utf8 DEFAULT NULL,
  `FromCountry` varchar(80) CHARACTER SET utf8 DEFAULT NULL,
  `ToICAO` char(4) CHARACTER SET utf8 DEFAULT NULL,
  `ToIATA` char(3) CHARACTER SET utf8 DEFAULT NULL,
  `ToName` varchar(80) CHARACTER SET utf8 DEFAULT NULL,
  `ToLat` double DEFAULT NULL,
  `ToLong` double DEFAULT NULL,
  `ToLocation` varchar(80) CHARACTER SET utf8 DEFAULT NULL,
  `ToCountry` varchar(80) CHARACTER SET utf8 DEFAULT NULL,
  `Interesting` tinyint(1) NOT NULL DEFAULT '0',
  `Mlat` tinyint(1) NOT NULL DEFAULT '0',
  `Track` varchar(60000) DEFAULT NULL,
  `Note` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `flightsos`
--
ALTER TABLE `flightstable`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `LastSeen` (`LastSeen`),
  ADD KEY `ModeS` (`ModeS`),
  ADD KEY `Registration` (`Registration`),
  ADD KEY `Operator` (`Operator`),
  ADD KEY `Callsign` (`Callsign`),
  ADD KEY `AircraftModel` (`AircraftModel`),
  ADD KEY `Interesting_Index` (`ModeS`,`Callsign`,`Interesting`,`LastSeen`) USING BTREE,
  ADD KEY `ModeS_LastSeen` (`ModeS`,`LastSeen`),
  ADD KEY `Interesting_ModeS` (`Interesting`,`ModeS`);
