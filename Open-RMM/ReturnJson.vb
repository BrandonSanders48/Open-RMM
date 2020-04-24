Module ReturnJson
	Public retText_Product As String = ""
	Public Sub getProduct()
		retText_Product = "{"
		Dim productList As List(Of Dictionary(Of String, String)) = MachineInfo.getProduct
		For i As Integer = 0 To productList.Count - 1
			retText_Product &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = productList(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_Product &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_Product = retText_Product.Remove(retText_Product.Length - 1, 1)
			retText_Product &= "},"
		Next
		retText_Product = retText_Product.Replace("\", "\\")
		retText_Product = retText_Product.Replace("&", "")
		retText_Product = retText_Product.Remove(retText_Product.Length - 1, 1)
		retText_Product &= "}"
	End Sub

	Public retText_OS As String = ""
	Public Sub getOS()
		retText_OS = "{"
		Dim operatingSystemList As List(Of Dictionary(Of String, String)) = MachineInfo.getOperatingSystem
		For i As Integer = 0 To operatingSystemList.Count - 1
			retText_OS &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = operatingSystemList(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_OS &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_OS = retText_OS.Remove(retText_OS.Length - 1, 1)
			retText_OS &= "},"
		Next
		retText_OS = retText_OS.Replace("\", "\\")
		retText_OS = retText_OS.Remove(retText_OS.Length - 1, 1)
		retText_OS &= "}"
	End Sub

	Public retText_ComputerSystem As String = ""
	Public Sub getComputerSystem()
		retText_ComputerSystem = "{"
		Dim computerSystemList As List(Of Dictionary(Of String, String)) = MachineInfo.getComputerSystem
		For i As Integer = 0 To computerSystemList.Count - 1
			retText_ComputerSystem &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = computerSystemList(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_ComputerSystem &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_ComputerSystem = retText_ComputerSystem.Remove(retText_ComputerSystem.Length - 1, 1)
			retText_ComputerSystem &= "},"
		Next
		retText_ComputerSystem = retText_ComputerSystem.Replace("\", "\\")
		retText_ComputerSystem = retText_ComputerSystem.Remove(retText_ComputerSystem.Length - 1, 1)
		retText_ComputerSystem &= "}"
	End Sub

	Public retText_BootConfig As String = ""
	Public Sub getBootConfig()
		retText_BootConfig = "{"
		Dim bootConfigurationList As List(Of Dictionary(Of String, String)) = MachineInfo.getBootConfiguration
		For i As Integer = 0 To bootConfigurationList.Count - 1
			retText_BootConfig &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = bootConfigurationList(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_BootConfig &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_BootConfig = retText_BootConfig.Remove(retText_BootConfig.Length - 1, 1)
			retText_BootConfig &= "},"
		Next
		retText_BootConfig = retText_BootConfig.Replace("\", "\\")
		retText_BootConfig = retText_BootConfig.Remove(retText_BootConfig.Length - 1, 1)
		retText_BootConfig &= "}"
	End Sub

	Public retText_LocalTime As String = ""
	Public Sub getLocalTime()
		retText_LocalTime = "{"
		Dim localTimeList As List(Of Dictionary(Of String, String)) = MachineInfo.getLocalTime
		For i As Integer = 0 To localTimeList.Count - 1
			retText_LocalTime &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = localTimeList(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_LocalTime &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_LocalTime = retText_LocalTime.Remove(retText_LocalTime.Length - 1, 1)
			retText_LocalTime &= "},"
		Next
		retText_LocalTime = retText_LocalTime.Replace("\", "\\")
		retText_LocalTime = retText_LocalTime.Remove(retText_LocalTime.Length - 1, 1)
		retText_LocalTime &= "}"
	End Sub

	Public retText_PnPEntity As String = ""
	Public Sub getPnPEntity()
		retText_PnPEntity = "{"
		Dim PnPEntityList As List(Of Dictionary(Of String, String)) = MachineInfo.getPnPEntity
		For i As Integer = 0 To PnPEntityList.Count - 1
			retText_PnPEntity &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = PnPEntityList(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_PnPEntity &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_PnPEntity = retText_PnPEntity.Remove(retText_PnPEntity.Length - 1, 1)
			retText_PnPEntity &= "},"
		Next
		retText_PnPEntity = retText_PnPEntity.Replace("\", "\\")
		retText_PnPEntity = retText_PnPEntity.Remove(retText_PnPEntity.Length - 1, 1)
		retText_PnPEntity &= "}"
	End Sub

	Public retText_LogonSession As String = ""
	Public Sub getLogonSession()
		retText_LogonSession = "{"
		Dim logonSessionList As List(Of Dictionary(Of String, String)) = MachineInfo.getLogonSession
		For i As Integer = 0 To logonSessionList.Count - 1
			retText_LogonSession &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = logonSessionList(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_LogonSession &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_LogonSession = retText_LogonSession.Remove(retText_LogonSession.Length - 1, 1)
			retText_LogonSession &= "},"
		Next
		retText_LogonSession = retText_LogonSession.Replace("\", "\\")
		retText_LogonSession = retText_LogonSession.Remove(retText_LogonSession.Length - 1, 1)
		retText_LogonSession &= "}"
	End Sub

	Public retText_NetworkLogin As String = ""
	Public Sub getNetworkLogin()
		retText_NetworkLogin = "{"
		Dim networkLoginProfileList As List(Of Dictionary(Of String, String)) = MachineInfo.getNetworkLoginProfile
		For i As Integer = 0 To networkLoginProfileList.Count - 1
			retText_NetworkLogin &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = networkLoginProfileList(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_NetworkLogin &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_NetworkLogin = retText_NetworkLogin.Remove(retText_NetworkLogin.Length - 1, 1)
			retText_NetworkLogin &= "},"
		Next
		retText_NetworkLogin = retText_NetworkLogin.Replace("\", "\\")
		retText_NetworkLogin = retText_NetworkLogin.Remove(retText_NetworkLogin.Length - 1, 1)
		retText_NetworkLogin &= "}"
	End Sub

	Public retText_UserAccount As String = ""
	Public Sub getUserAccount()
		retText_UserAccount = "{"
		Dim userAccountList As List(Of Dictionary(Of String, String)) = MachineInfo.getUserAccount
		For i As Integer = 0 To userAccountList.Count - 1
			retText_UserAccount &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = userAccountList(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_UserAccount &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_UserAccount = retText_UserAccount.Remove(retText_UserAccount.Length - 1, 1)
			retText_UserAccount &= "},"
		Next
		retText_UserAccount = retText_UserAccount.Replace("\", "\\")
		retText_UserAccount = retText_UserAccount.Remove(retText_UserAccount.Length - 1, 1)
		retText_UserAccount &= "}"
	End Sub

	Public retText_Group As String = ""
	Public Sub getGroup()
		retText_Group = "{"
		Dim groupList As List(Of Dictionary(Of String, String)) = MachineInfo.getGroup
		For i As Integer = 0 To groupList.Count - 1
			retText_Group &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = groupList(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_Group &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_Group = retText_Group.Remove(retText_Group.Length - 1, 1)
			retText_Group &= "},"
		Next
		retText_Group = retText_Group.Replace("\", "\\")
		retText_Group = retText_Group.Remove(retText_Group.Length - 1, 1)
		retText_Group &= "}"
	End Sub

	Public retText_CodecFile As String = ""
	Public Sub getCodecFile()
		retText_CodecFile = "{"
		Dim codecFileList As List(Of Dictionary(Of String, String)) = MachineInfo.getCodecFile
		For i As Integer = 0 To codecFileList.Count - 1
			retText_CodecFile &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = codecFileList(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_CodecFile &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_CodecFile = retText_CodecFile.Remove(retText_CodecFile.Length - 1, 1)
			retText_CodecFile &= "},"
		Next
		retText_CodecFile = retText_CodecFile.Replace("&", "")
		retText_CodecFile = retText_CodecFile.Replace("\", "\\")
		retText_CodecFile = retText_CodecFile.Remove(retText_CodecFile.Length - 1, 1)
		retText_CodecFile &= "}"
	End Sub

	Public retText_BaseBoard As String = ""
	Public Sub getBaseBoard()
		retText_BaseBoard = "{"
		Dim baseBoardList As List(Of Dictionary(Of String, String)) = MachineInfo.getBaseBoard
		For i As Integer = 0 To baseBoardList.Count - 1
			retText_BaseBoard &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = baseBoardList(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_BaseBoard &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_BaseBoard = retText_BaseBoard.Remove(retText_BaseBoard.Length - 1, 1)
			retText_BaseBoard &= "},"
		Next
		retText_BaseBoard = retText_BaseBoard.Replace("\", "\\")
		retText_BaseBoard = retText_BaseBoard.Remove(retText_BaseBoard.Length - 1, 1)
		retText_BaseBoard &= "}"
	End Sub

	Public retText_BIOS As String = ""
	Public Sub getBIOS()
		retText_BIOS = "{"
		Dim BIOSList As List(Of Dictionary(Of String, String)) = MachineInfo.getBIOS
		For i As Integer = 0 To BIOSList.Count - 1
			retText_BIOS &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = BIOSList(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_BIOS &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_BIOS = retText_BIOS.Remove(retText_BIOS.Length - 1, 1)
			retText_BIOS &= "},"
		Next
		retText_BIOS = retText_BIOS.Replace("\", "\\")
		retText_BIOS = retText_BIOS.Remove(retText_BIOS.Length - 1, 1)
		retText_BIOS &= "}"
	End Sub

	Public retText_Processor As String = ""
	Public Sub getProcessor()
		retText_Processor = "{"
		Dim processorList As List(Of Dictionary(Of String, String)) = MachineInfo.getProcessor
		For i As Integer = 0 To processorList.Count - 1
			retText_Processor &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = processorList(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_Processor &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_Processor = retText_Processor.Remove(retText_Processor.Length - 1, 1)
			retText_Processor &= "},"
		Next
		retText_Processor = retText_Processor.Replace("\", "\\")
		retText_Processor = retText_Processor.Replace("&", "")
		retText_Processor = retText_Processor.Remove(retText_Processor.Length - 1, 1)
		retText_Processor &= "}"
	End Sub

	Public retText_DesktopMonitor As String = ""
	Public Sub getDesktopMonitor()
		retText_DesktopMonitor = "{"
		Dim desktopMonitorList As List(Of Dictionary(Of String, String)) = MachineInfo.getDesktopMonitor
		For i As Integer = 0 To desktopMonitorList.Count - 1
			retText_DesktopMonitor &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = desktopMonitorList(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_DesktopMonitor &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_DesktopMonitor = retText_DesktopMonitor.Remove(retText_DesktopMonitor.Length - 1, 1)
			retText_DesktopMonitor &= "},"
		Next
		retText_DesktopMonitor = retText_DesktopMonitor.Replace("\", "\\")
		retText_DesktopMonitor = retText_DesktopMonitor.Replace("&", "")
		retText_DesktopMonitor = retText_DesktopMonitor.Remove(retText_DesktopMonitor.Length - 1, 1)
		retText_DesktopMonitor &= "}"
	End Sub

	Public retText_VideoController As String = ""
	Public Sub getVideoController()
		retText_VideoController = "{"
		Dim videoControllerList As List(Of Dictionary(Of String, String)) = MachineInfo.getVideoController
		For i As Integer = 0 To videoControllerList.Count - 1
			retText_VideoController &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = videoControllerList(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_VideoController &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_VideoController = retText_VideoController.Remove(retText_VideoController.Length - 1, 1)
			retText_VideoController &= "},"
		Next
		retText_VideoController = retText_VideoController.Replace("&", "")
		retText_VideoController = retText_VideoController.Replace("\", "\\")
		retText_VideoController = retText_VideoController.Remove(retText_VideoController.Length - 1, 1)
		retText_VideoController &= "}"
	End Sub

	Public retText_Keyboard As String = ""
	Public Sub getKeyboard()
		retText_Keyboard = "{"
		Dim keyboardList As List(Of Dictionary(Of String, String)) = MachineInfo.getKeyboard
		For i As Integer = 0 To keyboardList.Count - 1
			retText_Keyboard &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = keyboardList(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_Keyboard &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_Keyboard = retText_Keyboard.Remove(retText_Keyboard.Length - 1, 1)
			retText_Keyboard &= "},"
		Next
		retText_Keyboard = retText_Keyboard.Replace("&", "")
		retText_Keyboard = retText_Keyboard.Replace("\", "\\")
		retText_Keyboard = retText_Keyboard.Remove(retText_Keyboard.Length - 1, 1)
		retText_Keyboard &= "}"
	End Sub

	Public retText_PointingDevice As String = ""
	Public Sub getPointingDevice()
		retText_PointingDevice = "{"
		Dim pointingDeviceList As List(Of Dictionary(Of String, String)) = MachineInfo.getPointingDevice
		For i As Integer = 0 To pointingDeviceList.Count - 1
			retText_PointingDevice &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = pointingDeviceList(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_PointingDevice &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_PointingDevice = retText_PointingDevice.Remove(retText_PointingDevice.Length - 1, 1)
			retText_PointingDevice &= "},"
		Next
		retText_PointingDevice = retText_PointingDevice.Replace("&", "")
		retText_PointingDevice = retText_PointingDevice.Replace("\", "\\")
		retText_PointingDevice = retText_PointingDevice.Remove(retText_PointingDevice.Length - 1, 1)
		retText_PointingDevice &= "}"
	End Sub

	Public retText_1394Controller As String = ""
	Public Sub get1394Controller()
		retText_1394Controller = "{"
		Dim controller1394List As List(Of Dictionary(Of String, String)) = MachineInfo.get1394Controller
		For i As Integer = 0 To controller1394List.Count - 1
			retText_1394Controller &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = controller1394List(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_1394Controller &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_1394Controller = retText_1394Controller.Remove(retText_1394Controller.Length - 1, 1)
			retText_1394Controller &= "},"
		Next
		retText_1394Controller = retText_1394Controller.Replace("&", "")
		retText_1394Controller = retText_1394Controller.Replace("\", "\\")
		retText_1394Controller = retText_1394Controller.Remove(retText_1394Controller.Length - 1, 1)
		retText_1394Controller &= "}"
	End Sub

	Public retText_ParallelPort As String = ""
	Public Sub getParallelPort()
		retText_ParallelPort = "{"
		Dim parallelPortList As List(Of Dictionary(Of String, String)) = MachineInfo.getParallelPort
		For i As Integer = 0 To parallelPortList.Count - 1
			retText_ParallelPort &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = parallelPortList(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_ParallelPort &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_ParallelPort = retText_ParallelPort.Remove(retText_ParallelPort.Length - 1, 1)
			retText_ParallelPort &= "},"
		Next
		retText_ParallelPort = retText_ParallelPort.Replace("\", "\\")
		retText_ParallelPort = retText_ParallelPort.Remove(retText_ParallelPort.Length - 1, 1)
		retText_ParallelPort &= "}"
	End Sub

	Public retText_PCMCIAController As String = ""
	Public Sub getPCMCIAController()
		retText_PCMCIAController = "{"
		Dim PCMCIAControllerList As List(Of Dictionary(Of String, String)) = MachineInfo.getPCMCIAController
		For i As Integer = 0 To PCMCIAControllerList.Count - 1
			retText_PCMCIAController &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = PCMCIAControllerList(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_PCMCIAController &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_PCMCIAController = retText_PCMCIAController.Remove(retText_PCMCIAController.Length - 1, 1)
			retText_PCMCIAController &= "},"
		Next
		retText_PCMCIAController = retText_PCMCIAController.Replace("&", "")
		retText_PCMCIAController = retText_PCMCIAController.Replace("\", "\\")
		retText_PCMCIAController = retText_PCMCIAController.Remove(retText_PCMCIAController.Length - 1, 1)
		retText_PCMCIAController &= "}"
	End Sub

	Public retText_IDEControllerDevice As String = ""
	Public Sub getIDEControllerDevice()
		retText_IDEControllerDevice = "{"
		Dim IDEControllerList As List(Of Dictionary(Of String, String)) = MachineInfo.getIDEControllerDevice
		For i As Integer = 0 To IDEControllerList.Count - 1
			retText_IDEControllerDevice &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = IDEControllerList(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_IDEControllerDevice &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_IDEControllerDevice = retText_IDEControllerDevice.Remove(retText_IDEControllerDevice.Length - 1, 1)
			retText_IDEControllerDevice &= "},"
		Next
		retText_IDEControllerDevice = retText_IDEControllerDevice.Replace("&", "")
		retText_IDEControllerDevice = retText_IDEControllerDevice.Replace("\", "\\")
		retText_IDEControllerDevice = retText_IDEControllerDevice.Remove(retText_IDEControllerDevice.Length - 1, 1)
		retText_IDEControllerDevice &= "}"
	End Sub

	Public retText_SCSIController As String = ""
	Public Sub getSCSIController()
		retText_SCSIController = "{"
		Dim SCSIControllerList As List(Of Dictionary(Of String, String)) = MachineInfo.getSCSIController
		For i As Integer = 0 To SCSIControllerList.Count - 1
			retText_SCSIController &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = SCSIControllerList(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_SCSIController &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_SCSIController = retText_SCSIController.Remove(retText_SCSIController.Length - 1, 1)
			retText_SCSIController &= "},"
		Next
		retText_SCSIController = retText_SCSIController.Replace("&", "")
		retText_SCSIController = retText_SCSIController.Replace("\", "\\")
		retText_SCSIController = retText_SCSIController.Remove(retText_SCSIController.Length - 1, 1)
		retText_SCSIController &= "}"
	End Sub

	Public retText_SerialPort As String = ""
	Public Sub getSerialPort()
		retText_SerialPort = "{"
		Dim serialPortList As List(Of Dictionary(Of String, String)) = MachineInfo.getSerialPort
		For i As Integer = 0 To serialPortList.Count - 1
			retText_SerialPort &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = serialPortList(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_SerialPort &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_SerialPort = retText_SerialPort.Remove(retText_SerialPort.Length - 1, 1)
			retText_SerialPort &= "},"
		Next
		retText_SerialPort = retText_SerialPort.Replace("&", "")
		retText_SerialPort = retText_SerialPort.Replace("\", "\\")
		retText_SerialPort = retText_SerialPort.Remove(retText_SerialPort.Length - 1, 1)
		retText_SerialPort &= "}"
	End Sub

	Public retText_USBHub As String = ""
	Public Sub getUSBHub()
		retText_USBHub = "{"
		Dim USBHubList As List(Of Dictionary(Of String, String)) = MachineInfo.getUSBHub
		For i As Integer = 0 To USBHubList.Count - 1
			retText_USBHub &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = USBHubList(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_USBHub &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_USBHub = retText_USBHub.Remove(retText_USBHub.Length - 1, 1)
			retText_USBHub &= "},"
		Next
		retText_USBHub = retText_USBHub.Replace("&", "")
		retText_USBHub = retText_USBHub.Replace("\", "\\")
		retText_USBHub = retText_USBHub.Remove(retText_USBHub.Length - 1, 1)
		retText_USBHub &= "}"
	End Sub

	Public retText_SoundDevice As String = ""
	Public Sub getSoundDevice()
		retText_SoundDevice = "{"
		Dim soundDeviceList As List(Of Dictionary(Of String, String)) = MachineInfo.getSoundDevice
		For i As Integer = 0 To soundDeviceList.Count - 1
			retText_SoundDevice &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = soundDeviceList(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_SoundDevice &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_SoundDevice = retText_SoundDevice.Remove(retText_SoundDevice.Length - 1, 1)
			retText_SoundDevice &= "},"
		Next
		retText_SoundDevice = retText_SoundDevice.Replace("\", "\\")
		retText_SoundDevice = retText_SoundDevice.Replace("&", "")
		retText_SoundDevice = retText_SoundDevice.Remove(retText_SoundDevice.Length - 1, 1)
		retText_SoundDevice &= "}"
	End Sub

	Public retText_NetworkAdapters As String = ""
	Public Sub getNetworkAdapters()
		retText_NetworkAdapters = "{"
		Dim networkDeviceList As List(Of Dictionary(Of String, String)) = MachineInfo.getNetworkAdapters
		For i As Integer = 0 To networkDeviceList.Count - 1
			retText_NetworkAdapters &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = networkDeviceList(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_NetworkAdapters &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_NetworkAdapters = retText_NetworkAdapters.Remove(retText_NetworkAdapters.Length - 1, 1)
			retText_NetworkAdapters &= "},"
		Next
		retText_NetworkAdapters = retText_NetworkAdapters.Replace("\", "\\")
		retText_NetworkAdapters = retText_NetworkAdapters.Remove(retText_NetworkAdapters.Length - 1, 1)
		retText_NetworkAdapters &= "}"
	End Sub

	Public retText_LogicalDisk As String = ""
	Public Sub getLogicalDisk()
		retText_LogicalDisk = "{"
		Dim LogicalDisk As List(Of Dictionary(Of String, String)) = MachineInfo.getLogicalDisk
		For i As Integer = 0 To LogicalDisk.Count - 1
			retText_LogicalDisk &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = LogicalDisk(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_LogicalDisk &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_LogicalDisk = retText_LogicalDisk.Remove(retText_LogicalDisk.Length - 1, 1)
			retText_LogicalDisk &= "},"
		Next
		retText_LogicalDisk = retText_LogicalDisk.Replace("\", "\\")
		retText_LogicalDisk = retText_LogicalDisk.Remove(retText_LogicalDisk.Length - 1, 1)
		retText_LogicalDisk &= "}"
	End Sub

	Public retText_Printers As String = ""
	Public Sub getPrinters()
		retText_Printers = "{"
		Dim printerList As List(Of Dictionary(Of String, String)) = MachineInfo.getPrinters
		For i As Integer = 0 To printerList.Count - 1
			retText_Printers &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = printerList(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_Printers &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_Printers = retText_Printers.Remove(retText_Printers.Length - 1, 1)
			retText_Printers &= "},"
		Next
		retText_Printers = retText_Printers.Replace("\", "\\")
		retText_Printers = retText_Printers.Remove(retText_Printers.Length - 1, 1)
		retText_Printers &= "}"
	End Sub

	Public retText_MappedLogicalDisk As String = ""
	Public Sub getMappedLogicalDisk()
		retText_MappedLogicalDisk = "{"
		Dim MappedLogicalDisk As List(Of Dictionary(Of String, String)) = MachineInfo.getMappedLogicalDisk
		For i As Integer = 0 To MappedLogicalDisk.Count - 1
			retText_MappedLogicalDisk &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = MappedLogicalDisk(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_MappedLogicalDisk &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_MappedLogicalDisk = retText_MappedLogicalDisk.Remove(retText_MappedLogicalDisk.Length - 1, 1)
			retText_MappedLogicalDisk &= "},"
		Next
		retText_MappedLogicalDisk = retText_MappedLogicalDisk.Replace("\", "\\")
		retText_MappedLogicalDisk = retText_MappedLogicalDisk.Remove(retText_MappedLogicalDisk.Length - 1, 1)
		retText_MappedLogicalDisk &= "}"
	End Sub

	Public retText_PhysicalMemory As String = ""
	Public Sub getPhysicalMemory()
		retText_PhysicalMemory = "{"
		Dim PhysicalMemory As List(Of Dictionary(Of String, String)) = MachineInfo.getPhysicalMemory
		For i As Integer = 0 To PhysicalMemory.Count - 1
			retText_PhysicalMemory &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = PhysicalMemory(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_PhysicalMemory &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_PhysicalMemory = retText_PhysicalMemory.Remove(retText_PhysicalMemory.Length - 1, 1)
			retText_PhysicalMemory &= "},"
		Next
		retText_PhysicalMemory = retText_PhysicalMemory.Replace("\", "\\")
		retText_PhysicalMemory = retText_PhysicalMemory.Remove(retText_PhysicalMemory.Length - 1, 1)
		retText_PhysicalMemory &= "}"
	End Sub

	Public retText_OptionalFeatures As String = ""
	Public Sub getOptionalFeatures()
		retText_OptionalFeatures = "{"
		Dim OptionalFeatures As List(Of Dictionary(Of String, String)) = MachineInfo.getOptionalFeatures
		For i As Integer = 0 To OptionalFeatures.Count - 1
			retText_OptionalFeatures &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = OptionalFeatures(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_OptionalFeatures &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_OptionalFeatures = retText_OptionalFeatures.Remove(retText_OptionalFeatures.Length - 1, 1)
			retText_OptionalFeatures &= "},"
		Next
		retText_OptionalFeatures = retText_OptionalFeatures.Replace("\", "\\")
		retText_OptionalFeatures = retText_OptionalFeatures.Remove(retText_OptionalFeatures.Length - 1, 1)
		retText_OptionalFeatures &= "}"
	End Sub

	Public retText_Battery As String = ""
	Public Sub getBattery()
		retText_Battery = "{"
		Dim Battery As List(Of Dictionary(Of String, String)) = MachineInfo.getBattery
		For i As Integer = 0 To Battery.Count - 1
			retText_Battery &= """" & i & """ :{"
			Dim dic As Dictionary(Of String, String) = Battery(i)
			For Each keyvalpair As KeyValuePair(Of String, String) In dic
				retText_Battery &= """" & keyvalpair.Key & """:""" & keyvalpair.Value & ""","
			Next
			retText_Battery = retText_Battery.Remove(retText_Battery.Length - 1, 1)
			retText_Battery &= "},"
		Next
		retText_Battery = retText_Battery.Replace("\", "\\")
		retText_Battery = retText_Battery.Remove(retText_Battery.Length - 1, 1)
		retText_Battery &= "}"
	End Sub

	Public retText_Proccesses As String = ""
	Public Sub getProccess()
		retText_Proccesses = "{"
		Dim count As Integer = 0
		Dim processArr As Array = MachineInfo.getProcess
		For Each p As Process In processArr
			retText_Proccesses &= """" & count & """ :{"
			retText_Proccesses &= """Name"":""" & p.ProcessName & ""","
			retText_Proccesses &= """PID"":""" & p.Id.ToString() & ""","
			retText_Proccesses &= """MainWindowTitle"":""" & p.MainWindowTitle.ToString() & ""","
			retText_Proccesses = retText_Proccesses.Remove(retText_Proccesses.Length - 1, 1)
			retText_Proccesses &= "},"
			count = count + 1
		Next
		retText_Proccesses = retText_Proccesses.Replace("&", "")
		retText_Proccesses = retText_Proccesses.Replace("\", "\\")
		retText_Proccesses = retText_Proccesses.Remove(retText_Proccesses.Length - 1, 1)
		retText_Proccesses &= "}"
	End Sub

	Public retText_Services As String = ""
	Public Sub getServices()
		retText_Services = "{"
		Dim count As Integer = 0
		Dim rServList As List(Of RunningServiceStructure)
		rServList = MachineInfo.getRunningServices()
		For Each service As RunningServiceStructure In rServList
			retText_Services &= """" & count & """ :{"
			retText_Services &= """Name"":""" & service.Name & ""","
			retText_Services &= """DisplayName"":""" & service.DisplayName & ""","
			If service.Description <> Nothing Then
				If (service.Description.Contains("""")) Then
					retText_Services &= """Description"":""" & service.Description.Replace("""", "") & ""","
				Else
					retText_Services &= """Description"":""" & service.Description & ""","
				End If
			End If
			retText_Services = retText_Services.Remove(retText_Services.Length - 1, 1)
			retText_Services &= "},"
			count = count + 1
		Next
		retText_Services = retText_Services.Replace("?", "")
		retText_Services = retText_Services.Replace("\", "\\")
		retText_Services = retText_Services.Replace("&", "")
		retText_Services = retText_Services.Remove(retText_Services.Length - 1, 1)
		retText_Services &= "}"
	End Sub
End Module
