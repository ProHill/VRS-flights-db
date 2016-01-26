Dim WinScriptHost
Set WinScriptHost = CreateObject("WScript.Shell")
WinScriptHost.Run Chr(34) & "C:\sqlite\db_query.bat" & Chr(34), 0, true
WinScriptHost.Run "C:\Progra~2\putty\putty.exe -load flightimport", 0, true
Set WinScriptHost = Nothing
