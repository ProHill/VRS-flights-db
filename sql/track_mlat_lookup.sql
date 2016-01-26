CREATE TABLE IF NOT EXISTS `track_mlat_lookup` (
  `Id` int(11) NOT NULL,
  `Icao` varchar(6) NOT NULL,
  `Callsign` varchar(20) DEFAULT NULL,
  `Registration` varchar(20) DEFAULT NULL,
  `Mlat` tinyint(1) NOT NULL,
  `Track` varchar(60000) DEFAULT NULL,
  `MultiTrack` tinyint(1) DEFAULT '0',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `mlat_lookupos`
--
ALTER TABLE `track_mlat_lookup`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `Icao_Timestamp` (`Icao`,`Timestamp`);