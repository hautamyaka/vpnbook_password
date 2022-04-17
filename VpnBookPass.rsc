# VpnBookPass

# VPNBookScript v2 (look Evernote->Mikrotik->Password VPNBook. PHP+Mikrotik, or https://habr.com/post/420373/)
:global VPNBookpIfName "freevpn"
:global VPNBookServerAddresses {"pl226.vpnbook.com";"de4.vpnbook.com";"us1.vpnbook.com";"us2.vpnbook.com";"ca222.vpnbook.com";"ca198.vpnbook.com";"fr1.vpnbook.com";"fr8.vpnbook.com"}
#:if ([:typeof $VPNBookServerAddresses] != "array") do={
#  :set VPNBookServerAddresses {"pl226.vpnbook.com";"de4.vpnbook.com";"us1.vpnbook.com";"us2.vpnbook.com";"ca222.vpnbook.com";"ca198.vpnbook.com";"fr1.vpnbook.com";"fr8.vpnbook.com"}
#}

:global VPNurl "http://anapa-rg.ru/pass_vpnbook_my.php"
:global QSymbPass 7
:global VPNBookErr false
:global VPNBookPassFile "VPNBookPass.txt"
:global VPNBookPass
:global VPNBookRun
:global sysname [/system identity get name]
:global TToken "633000577:AAHTeeee5ebPf_M77omDrrrrrjHl5Dskhhw"
:global TChatId "724563490"

:global VPNBookServerIndex
:if ([:typeof $VPNBookServerIndex] != "num") do={:set VPNBookServerIndex 0}

:if ([/interface pptp-client get $VPNBookpIfName running]) do={
  :set VPNBookRun true
} else {
  :if (!$VPNBookRun) do={
    :set VPNBookServerIndex ($VPNBookServerIndex + 1)
    :if ($VPNBookServerIndex>=[:len $VPNBookServerAddresses]) do={:set VPNBookServerIndex 0}
  } else {
    :set VPNBookRun false
  }
  :if (![/interface pptp-client get $VPNBookpIfName disabled]) do={/interface pptp-client set $VPNBookpIfName disabled=yes}
  :do {/tool fetch url=$VPNurl dst-path=$VPNBookPassFile} on-error={:set VPNBookErr true}
  :delay 2
  :do {:set VPNBookPass [/file get $VPNBookPassFile contents]} on-error={:set VPNBookErr true}
  :if (!$VPNBookErr) do={
    :if ([/interface pptp-client get $VPNBookpIfName password] != [:pick $VPNBookPass 0 $QSymbPass]) do={/interface pptp-client set $VPNBookpIfName password=[:pick $VPNBookPass 0 $QSymbPass]}
    :if ([/interface pptp-client get $VPNBookpIfName connect-to] != $VPNBookServerAddresses->$VPNBookServerIndex) do={/interface pptp-client set $VPNBookpIfName connect-to=($VPNBookServerAddresses->$VPNBookServerIndex)}
    :log info ("VPNBook: Attempt to connect to: ".($VPNBookServerAddresses->$VPNBookServerIndex).". Password: $VPNBookPass")
    /interface pptp-client set $VPNBookpIfName disabled=no
    :put "$[/system clock get time] - Delay start"
    :delay 15
    :put "$[/system clock get time] - Delay end"
    :if ([/interface pptp-client get $VPNBookpIfName running]) do={
      /tool fetch url=("https://api.telegram.org/bot$TToken/sendmessage\?chat_id=$TChatId&text=$sysname, VPNBook: Attempt to connect to: ".($VPNBookServerAddresses->$VPNBookServerIndex).". Password: $VPNBookPass") keep-result=no
    }  
  }
}
