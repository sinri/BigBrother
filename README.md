# BigBrother
Web Server Monitor on Linux
----
"War is peace", "Freedom is slavery" and "Ignorance is strength"

## Parts of tools

* The Ministry of Love: Grant display views to show the server status.
* The Ministry of Peace: Send current system info to target server. 
* The Ministry of Plenty: Receive servers' info and record it into database.
* The Ministry of Truth: To get the system info for current time. 

## Telescreen

On client it is designed to be called by cronjob, such as once a minute, to collect client status and send that to server.

On server it is used as an API handler to receive client status information and save it to database.

## Visual System Status

List recent alive clients show the system status with live charts.

## License

BigBrother is provided free under License GPLv3.

Echarts (BSD) and jQuery (MIT) are used in generating web page.