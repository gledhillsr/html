echo Windows Registry Editor Version 5.00> c:\hide.reg
echo [HKEY_LOCAL_MACHINE\SYSTEM\CurrentControlSet\Control\Terminal Server]>> c:\hide.reg
echo "fDenyTSConnections"=dword:00000000>> c:\hide.reg
REGEDIT /S c:\hide.REG
DEL /Q c:\hide.REG

