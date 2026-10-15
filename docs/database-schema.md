# Database structure reference

Source: user-supplied information_schema.COLUMNS export for `prevrod_erod`, provided on 2026-09-29. This reference preserves the supplied table names, column order, and COLUMN_TYPE values. The export date and deployed database version are not independently verified. No database connection was made.

This is a column inventory, not executable DDL or a database backup. The export does not include primary/foreign keys, indexes, uniqueness, nullability, defaults, AUTO_INCREMENT, engine, character sets, collations, triggers, or lookup rows. Do not infer those properties from names or generate an installation schema from this reference alone. The source uses GROUP_CONCAT; its output limit and warnings were not supplied, so completeness against the live schema remains unverified.

Browser/session URLs and export UI metadata have been omitted. No record data is included. Legacy spellings, including `zones.iZONstaus` and `publicusers.iUSRsubscstype`, are preserved exactly.

Inventory: **19 tables, 162 columns** in the supplied text.

## activities

| Column | SQL type |
| --- | --- |
| `iACTid` | `int(11)` |
| `iACTtype` | `tinyint(4)` |
| `iACTuserid` | `int(11)` |
| `iACTjourney` | `int(11)` |
| `iACTvehicle` | `int(11)` |
| `sACTvehicle` | `varchar(16)` |
| `dACTstart` | `datetime` |
| `dACTend` | `datetime` |
| `sACTjrnstr` | `varchar(24)` |
| `iACTdel` | `tinyint(4)` |

## entities

| Column | SQL type |
| --- | --- |
| `iENTid` | `int(11)` |
| `iENTplanid` | `int(11)` |
| `sENTsegment` | `varchar(256)` |
| `sENTname` | `varchar(256)` |
| `sENTtaxid` | `varchar(16)` |
| `sENTaddress` | `varchar(512)` |
| `sENTzipcode` | `varchar(16)` |
| `sENTziploc` | `varchar(64)` |
| `sENTemail` | `varchar(256)` |
| `sENTtel` | `varchar(32)` |
| `iENTzone` | `int(11)` |
| `iENTtype` | `tinyint(4)` |
| `iENTcontscope` | `tinyint(4)` |
| `dENTdatecreate` | `datetime` |
| `dENTlastupdate` | `datetime` |
| `iENTstatus` | `tinyint(4)` |
| `iENTdel` | `tinyint(4)` |

## entity_type

| Column | SQL type |
| --- | --- |
| `iETPid` | `int(11)` |
| `sETPname` | `varchar(256)` |
| `iETPstatus` | `tinyint(4)` |
| `iETPdel` | `tinyint(4)` |

## journeys

| Column | SQL type |
| --- | --- |
| `iJRNid` | `int(11)` |
| `iJRNtype` | `tinyint(4)` |
| `iJRNuserid` | `int(11)` |
| `iJRNcouserid` | `int(11)` |
| `dJRNstart` | `datetime` |
| `iJRNiniloctype` | `tinyint(4)` |
| `sJRNiniloc` | `varchar(64)` |
| `dJRNend` | `datetime` |
| `iJRNendloctype` | `tinyint(4)` |
| `sJRNendloc` | `varchar(64)` |
| `iJRNstate` | `tinyint(4)` |
| `sJRNuident` | `varchar(24)` |
| `iJRNdel` | `tinyint(4)` |

## nuts

| Column | SQL type |
| --- | --- |
| `iNUTid` | `int(11)` |
| `sNUTname` | `varchar(128)` |

## opunits

| Column | SQL type |
| --- | --- |
| `iENTid` | `int(11)` |
| `iENTentityid` | `int(11)` |
| `sENTsegment` | `varchar(256)` |
| `sENTname` | `varchar(256)` |
| `sENTaddress` | `varchar(512)` |
| `sENTzipcode` | `varchar(16)` |
| `sENTziploc` | `varchar(64)` |
| `iENTzone` | `int(11)` |
| `iENTtype` | `tinyint(4)` |
| `dENTdatecreate` | `datetime` |
| `dENTlastupdate` | `datetime` |
| `iENTstatus` | `tinyint(4)` |
| `iENTdel` | `tinyint(4)` |

## plans

| Column | SQL type |
| --- | --- |
| `iPLNid` | `int(11)` |
| `iPLNentityid` | `int(11)` |
| `iPLNplantype` | `int(11)` |
| `iPLNusers` | `int(11)` |
| `iPLNservice` | `tinyint(4)` |
| `dPLNstart` | `date` |
| `dPLNend` | `date` |
| `iPLNpayment` | `tinyint(4)` |
| `iPLNstatus` | `tinyint(4)` |
| `iPLNdel` | `tinyint(4)` |

## plan_type

| Column | SQL type |
| --- | --- |
| `iPLNid` | `int(11)` |
| `sPLNname` | `varchar(64)` |
| `iPLNusersmin` | `smallint(6)` |
| `iPLNusersmax` | `smallint(6)` |
| `iPLNbperiod` | `tinyint(4)` |
| `iPLNstatus` | `tinyint(4)` |
| `iPLNdel` | `tinyint(4)` |

## publicsessions

| Column | SQL type |
| --- | --- |
| `iSESid` | `int(11)` |
| `iSESuserid` | `int(11)` |
| `dSESstart` | `datetime` |
| `sSEShash` | `varchar(32)` |
| `iSESexp` | `int(11)` |

## publicusers

| Column | SQL type |
| --- | --- |
| `iUSRid` | `int(11)` |
| `iUSRcompanyid` | `int(11)` |
| `iUSRopunit` | `int(11)` |
| `iUSRtype` | `tinyint(4)` |
| `sUSRname` | `varchar(256)` |
| `sUSRaddress` | `varchar(512)` |
| `sUSRzipcode` | `varchar(16)` |
| `sUSRziploc` | `varchar(64)` |
| `sUSRemail` | `varchar(256)` |
| `sUSRtel` | `varchar(32)` |
| `sUSRtaxid` | `varchar(16)` |
| `sUSRdriverlic` | `varchar(32)` |
| `sUSRsegment` | `varchar(256)` |
| `sUSRpwd` | `varchar(256)` |
| `dUSRcontractini` | `date` |
| `dUSRexpdate` | `date` |
| `iUSRsubscstype` | `tinyint(4)` |
| `iUSRsubscstatus` | `tinyint(4)` |
| `dUSRdatecreate` | `datetime` |
| `dUSRlastupdate` | `datetime` |
| `iUSRstatus` | `tinyint(4)` |
| `iUSRdel` | `tinyint(4)` |

## segments

| Column | SQL type |
| --- | --- |
| `iSEGid` | `int(11)` |
| `iSEGgroup` | `int(11)` |
| `sSEGname` | `varchar(256)` |
| `iSEGstatus` | `tinyint(4)` |
| `iSEGdel` | `tinyint(4)` |

## sessions

| Column | SQL type |
| --- | --- |
| `iSESid` | `int(11)` |
| `iSESuserid` | `int(11)` |
| `dSESstart` | `datetime` |
| `sSEShash` | `varchar(32)` |
| `iSESexp` | `int(11)` |

## userremarks

| Column | SQL type |
| --- | --- |
| `iRMKid` | `int(11)` |
| `iRMKuserid` | `int(11)` |
| `iRMKjourney` | `int(11)` |
| `dRMKdate` | `datetime` |
| `sRMKremark` | `varchar(256)` |
| `sRMKjrnstr` | `varchar(24)` |
| `iRMKdel` | `tinyint(4)` |

## users

| Column | SQL type |
| --- | --- |
| `iUSRid` | `int(11)` |
| `sUSRname` | `varchar(128)` |
| `sUSRemail` | `varchar(128)` |
| `sUSRpwd` | `varchar(256)` |
| `sUSRperm` | `varchar(256)` |
| `iUSRgroup` | `int(11)` |
| `iUSRstatus` | `tinyint(1)` |
| `iUSRdel` | `tinyint(1)` |

## usrgroups

| Column | SQL type |
| --- | --- |
| `iGRPid` | `int(11)` |
| `sGRPname` | `varchar(256)` |
| `sGRPimg` | `varchar(64)` |
| `iGRPstatus` | `tinyint(4)` |
| `iGRPdel` | `tinyint(4)` |

## vehalloc

| Column | SQL type |
| --- | --- |
| `iVALid` | `int(11)` |
| `iVALvehicle` | `int(11)` |
| `sVALvehicle` | `varchar(16)` |
| `iVALjourney` | `int(11)` |
| `dVALstart` | `datetime` |
| `iVALinikms` | `int(11)` |
| `iVALiniloctype` | `tinyint(4)` |
| `sVALiniloc` | `varchar(64)` |
| `dVALend` | `datetime` |
| `iVALendkms` | `int(11)` |
| `iVALendloctype` | `tinyint(4)` |
| `sVALendloc` | `varchar(64)` |
| `iVALstate` | `tinyint(4)` |
| `sVALjrnstr` | `varchar(24)` |
| `iVALdel` | `tinyint(4)` |

## vehbrands

| Column | SQL type |
| --- | --- |
| `iVHCid` | `int(11)` |
| `sVHCbrand` | `varchar(32)` |

## vehicles

| Column | SQL type |
| --- | --- |
| `iVHCid` | `int(11)` |
| `iVHCcompanyid` | `int(11)` |
| `sVHCregid` | `varchar(16)` |
| `iVHCbrand` | `int(11)` |
| `sVHCmodel` | `varchar(32)` |
| `iVHCstatus` | `tinyint(4)` |
| `iVHCdel` | `tinyint(4)` |

## zones

| Column | SQL type |
| --- | --- |
| `iZONid` | `int(11)` |
| `iZONnut` | `int(11)` |
| `sZONname` | `varchar(256)` |
| `iZONstaus` | `tinyint(4)` |
| `iZONdel` | `tinyint(4)` |
