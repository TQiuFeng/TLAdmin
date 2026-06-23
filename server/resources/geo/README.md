# Local Geo Data

Author: qiufeng

This directory stores local-only lookup data. No external API is used.

`phone_prefixes.csv`

```csv
prefix,province,city,operator,area_code,postcode
1380000,北京,北京,中国移动,010,100000
```

Lookup rule: match the longest prefix from the phone number. For Chinese mobile numbers, 7-digit prefixes are usually enough for attribution datasets.

`ip_ranges.csv`

```csv
start_ip,end_ip,country,province,city,isp
223.5.5.0,223.5.5.255,中国,浙江,杭州,阿里云
```

Lookup rule: convert IPv4 to unsigned integer and find the containing range.

IP location block rule:

- The block service reads `ip_ranges.csv` through `IpLocationService`.
- Rules can match `country`, `province`, `city`, `isp`, exact `ip`, `cidr`, `start_ip` and `end_ip`.
- The default config is disabled and stored in `config/security.php`; runtime changes are persisted to `runtime/config/system.json`.
- No external API is used by lookup or block checks.

Replace these sample files with complete licensed local datasets before production use.
