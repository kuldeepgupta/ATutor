<html><script language="vbscript">
		option explicit
		Dim IntervalID
		Dim count
		count = 0

		sub loaded

		    IntervalID = Window.setInterval("askServer",5000)
		end sub

		sub changedF2
		    askServer
		end sub

		sub askServer
		    Dim objAsp, theFile
		    set objAsp = CreateObject("Microsoft.XMLHTTP")
		    objAsp.open "GET", "../bing.php?uselessVar=" + CStr(count) + "&chatID=<?php echo $_SESSION['login']; ?>", false
		    objAsp.send()
		    theFile = objAsp.responsetext
		    if InStr(theFile,"yes") > 0 then
			Player.URL = "chime.wav"
		    else
		    end if
		    count = count + 1
		    document.f1.f2.value = CStr(count) + theFile
		    objAsp = 3
		    theFile = ""
		end sub

		</script>
		<body onLoad="loaded" language="vbscript">
		<form name=f1><input type=text name=f2 length="200" />sss</form>
		<OBJECT ID="Player" height="0" width="0" CLASSID="CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6"></OBJECT>
		</body>
		</html>
