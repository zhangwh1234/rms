/*
* CCLink Library v2.0
* Program by cuichenguang
* 2018-07-23
* CCLink2008.js?DefaultHost=192.168.0.2&DefaultPort=2009
*/

Version = "2.0";

TCCLinkExt = function () {
}
TCCLinkExt.prototype.Assigned = 0;
TCCLinkExt.prototype.ExtCode = "";
TCCLinkExt.prototype.ExtPassWD = "";
TCCLinkExt.prototype.ExtWorkNo = "";
TCCLinkExt.prototype.ExtName = "";
TCCLinkExt.prototype.ExtCaller = "";
TCCLinkExt.prototype.ExtType = 0;
TCCLinkExt.prototype.CallState = 0;
TCCLinkExt.prototype.CallDirect = 0;
TCCLinkExt.prototype.ExtDoNotDisturbTime = "";
TCCLinkExt.prototype.ExtDoNotDisturb = 0;
TCCLinkExt.prototype.LoginMode = "";
TCCLinkExt.prototype.LastCallResult = 0;
TCCLinkExt.prototype.ANI = "";
TCCLinkExt.prototype.DNIS = "";
TCCLinkExt.prototype.ChannelData = "";
TCCLinkExt.prototype.OtherANI = "";
TCCLinkExt.prototype.OtherDNIS = "";
TCCLinkExt.prototype.OtherChannelData = "";
TCCLinkExt.prototype.CallSessionID = "";
TCCLinkExt.prototype.CallIndex = 0;
TCCLinkExt.prototype.CallTime = "";
TCCLinkExt.prototype.ConnectTime = "";
TCCLinkExt.prototype.RecordFile = "";
TCCLinkExt.prototype.OtherCode = "";
TCCLinkExt.prototype.OtherRole = 0;
TCCLinkExt.prototype.TransferCode = "";
TCCLinkExt.prototype.TransferRole = 0;
TCCLinkExt.prototype.CallData = "";
TCCLinkExt.prototype.QueueState = 0;

TCCLinkQueueExt = function () {
}
TCCLinkQueueExt.prototype.ExtCode = "";
TCCLinkQueueExt.prototype.Pri = 0;

TCCLinkQueueUser = function () {
}
TCCLinkQueueUser.prototype.UserID = "";
TCCLinkQueueUser.prototype.UserCode = "";
TCCLinkQueueUser.prototype.UserRole = 0;
TCCLinkQueueUser.prototype.UserEnterTime = "";

TCCLinkQueue = function () {
}
TCCLinkQueue.prototype.Assigned = 0;
TCCLinkQueue.prototype.QueueCode = "";
TCCLinkQueue.prototype.QueueName = "";
TCCLinkQueue.prototype.ExtList = new Array();
TCCLinkQueue.prototype.UserList = new Array();

TCCLinkMeetingRoomMember = function () {
}
TCCLinkMeetingRoomMember.prototype.MemberID = "";
TCCLinkMeetingRoomMember.prototype.MemberCode = "";
TCCLinkMeetingRoomMember.prototype.MemberRole = 0;
TCCLinkMeetingRoomMember.prototype.MemberType = 0;
TCCLinkMeetingRoomMember.prototype.EnterTime = "";
TCCLinkMeetingRoomMember.prototype.ChannelData = "";

TCCLinkMeetingRoom = function () {
}
TCCLinkMeetingRoom.prototype.Assigned = 0;
TCCLinkMeetingRoom.prototype.MeetingRoomCode = "";
TCCLinkMeetingRoom.prototype.MeetingRoomPassWD = "";
TCCLinkMeetingRoom.prototype.MeetingRoomName = "";
TCCLinkMeetingRoom.prototype.MeetingRoomMode = 0;
TCCLinkMeetingRoom.prototype.MemberList = new Array();

TCCLinkTrunk = function () {
}
TCCLinkTrunk.prototype.Assigned = 0;
TCCLinkTrunk.prototype.ChannelNo = 0;
TCCLinkTrunk.prototype.CallState = 0;
TCCLinkTrunk.prototype.ChannelCode = "";
TCCLinkTrunk.prototype.ChannelRole = 0;
TCCLinkTrunk.prototype.CallSessionID = "";
TCCLinkTrunk.prototype.CallDirect = 0;
TCCLinkTrunk.prototype.ANI = "";
TCCLinkTrunk.prototype.DNIS = "";
TCCLinkTrunk.prototype.ChannelData = "";
TCCLinkTrunk.prototype.CallTime = "";
TCCLinkTrunk.prototype.ConnectTime = "";
TCCLinkTrunk.prototype.OtherCode = "";
TCCLinkTrunk.prototype.OtherRole = 0;
TCCLinkTrunk.prototype.ChannelInfo = "";
TCCLinkTrunk.prototype.LogEnabled = 0;

TCCLink = function () {
    var scripts = document.getElementsByTagName('script');
    for (i in scripts) {
        if (scripts[i].src && scripts[i].src.indexOf('CCLink2008') !== -1) {
            var Requests = scripts[i].src.split("?");
            if (typeof (Requests[1]) == "string") {
                Requests = Requests[1].split("&");
                var Request = {};
                for (var i in Requests) {
                    var j = Requests[i].split("=");
                    Request[j[0]] = decodeURIComponent(j[1]);
                }
                if (Request["DefaultHost"]) {
                    this.CCLinkDefaultHost = Request["DefaultHost"];
                }
                else {
                    this.CCLinkDefaultHost = "127.0.0.1";
                }
                if (Request["DefaultPort"]) {
                    this.CCLinkDefaultPort = Request["DefaultPort"];
                }
                else {
                    this.CCLinkDefaultPort = 3008;
                }
            }
            break;
        }
    }
}
TCCLink.prototype.CCLinkSessionID = "";
TCCLink.prototype.CCLinkState = 0;
TCCLink.prototype.CCLinkClientIP = "";
TCCLink.prototype.CCLinkInvokeFailTime = new Date();
TCCLink.prototype.CCLinkRefreshTime = new Date();
TCCLink.prototype.CCLinkDefaultHost = "127.0.0.1";
TCCLink.prototype.CCLinkDefaultPort = 3008;
TCCLink.prototype.CCLinkUrl = "";
TCCLink.prototype.CCLinkEventID = 0;
TCCLink.prototype.CCLinkExtList = new Array();
TCCLink.prototype.CCLinkQueueList = new Array();
TCCLink.prototype.CCLinkTrunkList = new Array();
TCCLink.prototype.CCLinkMeetingRoomList = new Array();
TCCLink.prototype.Link_ConnectServer = function (Host, Port, SynchronizationTime) {
    var CCServerHost = "";
    if (Host.indexOf(":") >= 0) {
        CCServerHost = Host;
        this.CCLinkUrl = "http://127.0.0.1:" + Port + "/";
    }
    else {
        this.CCLinkUrl = "http://" + Host + ":" + Port + "/";
    }
    this.CCLinkState = 0;
    this.CCLinkInvokeFailTime = (new Date()) - 3600000;
    this.CCLinkSessionID = "";
    for (var i = 1; i <= 32; i++) {
        var n = Math.floor(Math.random() * 16.0).toString(16);
        this.CCLinkSessionID += n;
        if ((i == 8) || (i == 12) || (i == 16) || (i == 20)) {
            this.CCLinkSessionID += "-";
        }
    }
    var data = "?Action=" + encodeURIComponent("Link_ConnectServer")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&Version=" + encodeURIComponent(Version)
             + "&CCServerHost=" + encodeURIComponent(CCServerHost)
             + "&SynchronizationTime=" + encodeURIComponent(SynchronizationTime);
    CCLinkActionRequest(data);
}
TCCLink.prototype.Link_DisconnectServer = function () {
    this.CCLinkState = 0;
    var data = "?Action=" + encodeURIComponent("Link_DisconnectServer")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID);
    CCLinkActionRequest(data);
}
TCCLink.prototype.Link_GetState = function () {
    return this.CCLinkState;
}
TCCLink.prototype.Link_GetClientIP = function () {
    return this.CCLinkClientIP;
}
TCCLink.prototype.LinkEvent_OnConnected = null;
TCCLink.prototype.LinkEvent_OnDisconnected = null;
TCCLink.prototype.ExtIndexByCode = function (ExtCode) {
    var index = -1;
    for (var fori = 0; fori < this.CCLinkExtList.length; fori++) {
        if (this.CCLinkExtList[fori].ExtCode == ExtCode) {
            index = fori;
            break;
        }
    }
    return index;
}
TCCLink.prototype.Ext_GetCodeList = function (Separator) {
    sExtCodeList = "";
    if (this.CCLinkState != 0) {
        for (var fori = 0; fori < this.CCLinkExtList.length; fori++) {
            if (sExtCodeList == "") {
                sExtCodeList = this.CCLinkExtList[fori].ExtCode;
            }
            else {
                sExtCodeList = sExtCodeList + Separator + this.CCLinkExtList[fori].ExtCode;
            }
        }
    }
    return sExtCodeList;
}
TCCLink.prototype.Ext_Assign = function (ExtCode, ExtPassword) {
    if (this.CCLinkState == 0) {
        return "服务器未连接!";
    }
    if (ExtCode == "*" && ExtPassword == "*") {
        for (var fori = 0; fori < this.CCLinkExtList.length; fori++) {
            this.CCLinkExtList[fori].Assigned = 1;
        }
        var data = "?Action=" + encodeURIComponent("Ext_Assign")
                 + "&ActionTime=" + encodeURIComponent((new Date).getTime())
                 + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
                 + "&ExtCode=" + encodeURIComponent(ExtCode)
                 + "&ExtPassword=" + encodeURIComponent(ExtPassword);
        CCLinkActionRequest(data);
    }
    else {
        var ExtIndex = this.ExtIndexByCode(ExtCode);
        if (ExtIndex == -1) {
            return "分机号错误！";
        }
        if (ExtPassword != this.CCLinkExtList[ExtIndex].ExtPassWD) {
            //return "分机密码错误！";
        }
        this.CCLinkExtList[ExtIndex].Assigned = 1;
        var data = "?Action=" + encodeURIComponent("Ext_Assign")
                 + "&ActionTime=" + encodeURIComponent((new Date).getTime())
                 + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
                 + "&ExtCode=" + encodeURIComponent(ExtCode)
                 + "&ExtPassword=" + encodeURIComponent(ExtPassword);
        CCLinkActionRequest(data);
    }
    return "";
}
TCCLink.prototype.Ext_Deassign = function (ExtCode) {
    if (this.CCLinkState == 0) {
        return;
    }
    if (ExtCode == "*") {
        for (var fori = 0; fori < this.CCLinkExtList.length; fori++) {
            this.CCLinkExtList[fori].Assigned = 0;
        }
        var data = "?Action=" + encodeURIComponent("Ext_Deassign")
                 + "&ActionTime=" + encodeURIComponent((new Date).getTime())
                 + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
                 + "&ExtCode=" + encodeURIComponent(ExtCode);
        CCLinkActionRequest(data);
    }
    else {
        var ExtIndex = this.ExtIndexByCode(ExtCode);
        if (ExtIndex != -1) {
            this.CCLinkExtList[ExtIndex].Assigned = 0;
        }
        var data = "?Action=" + encodeURIComponent("Ext_Deassign")
                 + "&ActionTime=" + encodeURIComponent((new Date).getTime())
                 + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
                 + "&ExtCode=" + encodeURIComponent(ExtCode);
        CCLinkActionRequest(data);
    }
}
TCCLink.prototype.Ext_GetExtType = function (ExtCode) {
    var iResult = 0;
    if (this.CCLinkState != 0) {
        var ExtIndex = this.ExtIndexByCode(ExtCode);
        if (ExtIndex != -1) {
            iResult = this.CCLinkExtList[ExtIndex].ExtType;
        }
    }
    return iResult;
}
TCCLink.prototype.Ext_GetCallState = function (ExtCode) {
    var iResult = -1;
    if (this.CCLinkState != 0) {
        var ExtIndex = this.ExtIndexByCode(ExtCode);
        if (ExtIndex != -1) {
            iResult = this.CCLinkExtList[ExtIndex].CallState;
        }
    }
    return iResult;
}
TCCLink.prototype.Ext_GetLoginMode = function (ExtCode) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var ExtIndex = this.ExtIndexByCode(ExtCode);
        if (ExtIndex != -1) {
            sResult = this.CCLinkExtList[ExtIndex].LoginMode;
        }
    }
    return sResult;
}
TCCLink.prototype.Ext_SetLogoutOnLinkDisconnected = function (ExtCode, Value) {
    var data = "?Action=" + encodeURIComponent("Ext_SetLogoutOnLinkDisconnected")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&ExtCode=" + encodeURIComponent(ExtCode)
             + "&Value=" + encodeURIComponent(Value);
    CCLinkActionRequest(data);
}
TCCLink.prototype.Ext_GetExtName = function (ExtCode) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var ExtIndex = this.ExtIndexByCode(ExtCode);
        if (ExtIndex != -1) {
            sResult = this.CCLinkExtList[ExtIndex].ExtName;
        }
    }
    return sResult;
}
TCCLink.prototype.Ext_SetExtName = function (ExtCode, Value) {
    var data = "?Action=" + encodeURIComponent("Ext_SetExtName")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&ExtCode=" + encodeURIComponent(ExtCode)
             + "&Value=" + encodeURIComponent(Value);
    CCLinkActionRequest(data);
}
TCCLink.prototype.Ext_GetExtWorkNo = function (ExtCode) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var ExtIndex = this.ExtIndexByCode(ExtCode);
        if (ExtIndex != -1) {
            sResult = this.CCLinkExtList[ExtIndex].ExtWorkNo;
        }
    }
    return sResult;
}
TCCLink.prototype.Ext_SetExtWorkNo = function (ExtCode, Value) {
    var data = "?Action=" + encodeURIComponent("Ext_SetExtWorkNo")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&ExtCode=" + encodeURIComponent(ExtCode)
             + "&Value=" + encodeURIComponent(Value);
    CCLinkActionRequest(data);
}
TCCLink.prototype.Ext_GetExtCaller = function (ExtCode) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var ExtIndex = this.ExtIndexByCode(ExtCode);
        if (ExtIndex != -1) {
            sResult = this.CCLinkExtList[ExtIndex].ExtCaller;
        }
    }
    return sResult;
}
TCCLink.prototype.Ext_SetExtCaller = function (ExtCode, Value) {
    var data = "?Action=" + encodeURIComponent("Ext_SetExtCaller")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&ExtCode=" + encodeURIComponent(ExtCode)
             + "&Value=" + encodeURIComponent(Value);
    CCLinkActionRequest(data);
}
TCCLink.prototype.Ext_GetExtDoNotDisturb = function (ExtCode) {
    var iResult = 0;
    if (this.CCLinkState != 0) {
        var ExtIndex = this.ExtIndexByCode(ExtCode);
        if (ExtIndex != -1) {
            iResult = this.CCLinkExtList[ExtIndex].ExtDoNotDisturb;
        }
    }
    return iResult;
}
TCCLink.prototype.Ext_SetExtDoNotDisturb = function (ExtCode, Value) {
    var data = "?Action=" + encodeURIComponent("Ext_SetExtDoNotDisturb")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&ExtCode=" + encodeURIComponent(ExtCode)
             + "&Value=" + encodeURIComponent(Value);
    CCLinkActionRequest(data);
}
TCCLink.prototype.Ext_GetQueueState = function (ExtCode) {
    var iResult = 0;
    if (this.CCLinkState != 0) {
        var ExtIndex = this.ExtIndexByCode(ExtCode);
        if (ExtIndex != -1) {
            iResult = this.CCLinkExtList[ExtIndex].QueueState;
        }
    }
    return iResult;
}
TCCLink.prototype.Ext_GetCallInfo_CallSessionID = function (ExtCode) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var ExtIndex = this.ExtIndexByCode(ExtCode);
        if (ExtIndex != -1) {
            sResult = this.CCLinkExtList[ExtIndex].CallSessionID;
        }
    }
    return sResult;
}
TCCLink.prototype.Ext_GetCallInfo_CallIndex = function (ExtCode) {
    var iResult = 0;
    if (this.CCLinkState != 0) {
        var ExtIndex = this.ExtIndexByCode(ExtCode);
        if (ExtIndex != -1) {
            iResult = this.CCLinkExtList[ExtIndex].CallIndex;
        }
    }
    return iResult;
}
TCCLink.prototype.Ext_GetCallInfo_CallDirect = function (ExtCode) {
    var iResult = 0;
    if (this.CCLinkState != 0) {
        var ExtIndex = this.ExtIndexByCode(ExtCode);
        if (ExtIndex != -1) {
            iResult = this.CCLinkExtList[ExtIndex].CallDirect;
        }
    }
    return iResult;
}
TCCLink.prototype.Ext_GetCallInfo_ANI = function (ExtCode) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var ExtIndex = this.ExtIndexByCode(ExtCode);
        if (ExtIndex != -1) {
            sResult = this.CCLinkExtList[ExtIndex].ANI;
        }
    }
    return sResult;
}
TCCLink.prototype.Ext_GetCallInfo_DNIS = function (ExtCode) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var ExtIndex = this.ExtIndexByCode(ExtCode);
        if (ExtIndex != -1) {
            sResult = this.CCLinkExtList[ExtIndex].DNIS;
        }
    }
    return sResult;
}
TCCLink.prototype.Ext_GetCallInfo_OtherANI = function (ExtCode) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var ExtIndex = this.ExtIndexByCode(ExtCode);
        if (ExtIndex != -1) {
            sResult = this.CCLinkExtList[ExtIndex].OtherANI;
        }
    }
    return sResult;
}
TCCLink.prototype.Ext_GetCallInfo_OtherDNIS = function (ExtCode) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var ExtIndex = this.ExtIndexByCode(ExtCode);
        if (ExtIndex != -1) {
            sResult = this.CCLinkExtList[ExtIndex].OtherDNIS;
        }
    }
    return sResult;
}
TCCLink.prototype.Ext_GetCallInfo_OtherChannelData = function (ExtCode) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var ExtIndex = this.ExtIndexByCode(ExtCode);
        if (ExtIndex != -1) {
            sResult = this.CCLinkExtList[ExtIndex].OtherChannelData;
        }
    }
    return sResult;
}
TCCLink.prototype.Ext_GetCallInfo_CallTime = function (ExtCode) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var ExtIndex = this.ExtIndexByCode(ExtCode);
        if (ExtIndex != -1) {
            sResult = this.CCLinkExtList[ExtIndex].CallTime;
        }
    }
    return sResult;
}
TCCLink.prototype.Ext_GetCallInfo_ConnectTime = function (ExtCode) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var ExtIndex = this.ExtIndexByCode(ExtCode);
        if (ExtIndex != -1) {
            sResult = this.CCLinkExtList[ExtIndex].ConnectTime;
        }
    }
    return sResult;
}
TCCLink.prototype.Ext_GetCallInfo_RecordFile = function (ExtCode) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var ExtIndex = this.ExtIndexByCode(ExtCode);
        if (ExtIndex != -1) {
            sResult = this.CCLinkExtList[ExtIndex].RecordFile;
        }
    }
    return sResult;
}
TCCLink.prototype.Ext_GetCallInfo_OtherCode = function (ExtCode) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var ExtIndex = this.ExtIndexByCode(ExtCode);
        if (ExtIndex != -1) {
            sResult = this.CCLinkExtList[ExtIndex].OtherCode;
        }
    }
    return sResult;
}
TCCLink.prototype.Ext_GetCallInfo_OtherRole = function (ExtCode) {
    var iResult = 0;
    if (this.CCLinkState != 0) {
        var ExtIndex = this.ExtIndexByCode(ExtCode);
        if (ExtIndex != -1) {
            iResult = this.CCLinkExtList[ExtIndex].OtherRole;
        }
    }
    return iResult;
}
TCCLink.prototype.Ext_GetCallInfo_TransferCode = function (ExtCode) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var ExtIndex = this.ExtIndexByCode(ExtCode);
        if (ExtIndex != -1) {
            sResult = this.CCLinkExtList[ExtIndex].TransferCode;
        }
    }
    return sResult;
}
TCCLink.prototype.Ext_GetCallInfo_TransferRole = function (ExtCode) {
    var iResult = 0;
    if (this.CCLinkState != 0) {
        var ExtIndex = this.ExtIndexByCode(ExtCode);
        if (ExtIndex != -1) {
            iResult = this.CCLinkExtList[ExtIndex].TransferRole;
        }
    }
    return iResult;
}
TCCLink.prototype.Ext_GetCallInfo_CallData = function (ExtCode) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var ExtIndex = this.ExtIndexByCode(ExtCode);
        if (ExtIndex != -1) {
            sResult = this.CCLinkExtList[ExtIndex].CallData;
        }
    }
    return sResult;
}
TCCLink.prototype.Ext_GetCallInfo_LastCallResult = function (ExtCode) {
    var iResult = 0;
    if (this.CCLinkState != 0) {
        var ExtIndex = this.ExtIndexByCode(ExtCode);
        if (ExtIndex != -1) {
            iResult = this.CCLinkExtList[ExtIndex].LastCallResult;
        }
    }
    return iResult;
}
TCCLink.prototype.Ext_LoginToInnerChannel = function (ExtCode, ChannelNo) {
    var data = "?Action=" + encodeURIComponent("Ext_LoginToInnerChannel")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&ExtCode=" + encodeURIComponent(ExtCode)
             + "&ChannelNo=" + encodeURIComponent(ChannelNo);
    CCLinkActionRequest(data);
}
TCCLink.prototype.Ext_LoginToOutLineCode = function (ExtCode, OutLineCode) {
    var data = "?Action=" + encodeURIComponent("Ext_LoginToOutLineCode")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&ExtCode=" + encodeURIComponent(ExtCode)
             + "&OutLineCode=" + encodeURIComponent(OutLineCode);
    CCLinkActionRequest(data);
}
TCCLink.prototype.Ext_Logout = function (ExtCode) {
    var data = "?Action=" + encodeURIComponent("Ext_Logout")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&ExtCode=" + encodeURIComponent(ExtCode);
    CCLinkActionRequest(data);
}
TCCLink.prototype.Ext_AnswerCall = function (ExtCode) {
    var data = "?Action=" + encodeURIComponent("Ext_AnswerCall")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&ExtCode=" + encodeURIComponent(ExtCode);
    CCLinkActionRequest(data);
}
TCCLink.prototype.Ext_DropCall = function (ExtCode) {
    var data = "?Action=" + encodeURIComponent("Ext_DropCall")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&ExtCode=" + encodeURIComponent(ExtCode);
    CCLinkActionRequest(data);
}
TCCLink.prototype.Ext_MakeCall = function (ExtCode, DestCode) {
    var data = "?Action=" + encodeURIComponent("Ext_MakeCall")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&ExtCode=" + encodeURIComponent(ExtCode)
             + "&DestCode=" + encodeURIComponent(DestCode);
    CCLinkActionRequest(data);
}
TCCLink.prototype.Ext_Monitor = function (ExtCode, DestCode) {
    var data = "?Action=" + encodeURIComponent("Ext_Monitor")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&ExtCode=" + encodeURIComponent(ExtCode)
             + "&DestCode=" + encodeURIComponent(DestCode);
    CCLinkActionRequest(data);
}
TCCLink.prototype.Ext_ForceInsert = function (ExtCode, DestCode) {
    var data = "?Action=" + encodeURIComponent("Ext_ForceInsert")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&ExtCode=" + encodeURIComponent(ExtCode)
             + "&DestCode=" + encodeURIComponent(DestCode);
    CCLinkActionRequest(data);
}
TCCLink.prototype.Ext_PickUp = function (ExtCode, DestCode) {
    var data = "?Action=" + encodeURIComponent("Ext_PickUp")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&ExtCode=" + encodeURIComponent(ExtCode)
             + "&DestCode=" + encodeURIComponent(DestCode);
    CCLinkActionRequest(data);
}
TCCLink.prototype.Ext_TransferCall = function (ExtCode, DestCode, CallData, Options) {
    var data = "?Action=" + encodeURIComponent("Ext_TransferCall")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&ExtCode=" + encodeURIComponent(ExtCode)
             + "&DestCode=" + encodeURIComponent(DestCode)
             + "&CallData=" + encodeURIComponent(CallData)
             + "&Options=" + encodeURIComponent(Options);
    CCLinkActionRequest(data);
}
TCCLink.prototype.Ext_CancelTransfer = function (ExtCode) {
    var data = "?Action=" + encodeURIComponent("Ext_CancelTransfer")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&ExtCode=" + encodeURIComponent(ExtCode);
    CCLinkActionRequest(data);
}
TCCLink.prototype.Ext_ThreeParty = function (ExtCode) {
    var data = "?Action=" + encodeURIComponent("Ext_ThreeParty")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&ExtCode=" + encodeURIComponent(ExtCode);
    CCLinkActionRequest(data);
}
TCCLink.prototype.Ext_SendMessage = function (ExtCode, DestCode, Message) {
    var data = "?Action=" + encodeURIComponent("Ext_SendMessage")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&ExtCode=" + encodeURIComponent(ExtCode)
             + "&DestCode=" + encodeURIComponent(DestCode)
             + "&Message=" + encodeURIComponent(Message);
    CCLinkActionRequest(data);
}
TCCLink.prototype.Ext_PlayVoice = function (ExtCode, VoiceContext) {
    var data = "?Action=" + encodeURIComponent("Ext_PlayVoice")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&ExtCode=" + encodeURIComponent(ExtCode)
             + "&VoiceContext=" + encodeURIComponent(VoiceContext);
    CCLinkActionRequest(data);
}
TCCLink.prototype.Ext_StopPlayVoice = function (ExtCode) {
    var data = "?Action=" + encodeURIComponent("Ext_StopPlayVoice")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&ExtCode=" + encodeURIComponent(ExtCode);
    CCLinkActionRequest(data);
}
TCCLink.prototype.Ext_RegQueue = function (ExtCode, QueueList) {
    var data = "?Action=" + encodeURIComponent("Ext_RegQueue")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&ExtCode=" + encodeURIComponent(ExtCode)
             + "&QueueList=" + encodeURIComponent(QueueList);
    CCLinkActionRequest(data);
}
TCCLink.prototype.Ext_UnRegQueue = function (ExtCode, QueueList) {
    var data = "?Action=" + encodeURIComponent("Ext_UnRegQueue")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&ExtCode=" + encodeURIComponent(ExtCode)
             + "&QueueList=" + encodeURIComponent(QueueList);
    CCLinkActionRequest(data);
}
TCCLink.prototype.Ext_CheckInQueue = function (ExtCode) {
    var data = "?Action=" + encodeURIComponent("Ext_CheckInQueue")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&ExtCode=" + encodeURIComponent(ExtCode);
    CCLinkActionRequest(data);
}
TCCLink.prototype.Ext_CheckOutQueue = function (ExtCode) {
    var data = "?Action=" + encodeURIComponent("Ext_CheckOutQueue")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&ExtCode=" + encodeURIComponent(ExtCode);
    CCLinkActionRequest(data);
}
TCCLink.prototype.Ext_SaveConfig = function (ExtCode) {
    var data = "?Action=" + encodeURIComponent("Ext_SaveConfig")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&ExtCode=" + encodeURIComponent(ExtCode);
    CCLinkActionRequest(data);
}
TCCLink.prototype.ExtEvent_OnAssigned = null;
TCCLink.prototype.ExtEvent_OnLogin = null;
TCCLink.prototype.ExtEvent_OnLogout = null;
TCCLink.prototype.ExtEvent_OnCallStateChange = null;
TCCLink.prototype.ExtEvent_OnCallIn = null;
TCCLink.prototype.ExtEvent_OnConnected = null;
TCCLink.prototype.ExtEvent_OnConsultation = null;
TCCLink.prototype.ExtEvent_OnDisconnected = null;
TCCLink.prototype.ExtEvent_OnCallFinished = null;
TCCLink.prototype.ExtEvent_OnReceiveMessage = null;
TCCLink.prototype.ExtEvent_OnDoNotDisturbChange = null;
TCCLink.prototype.ExtEvent_OnPlayVoiceEnd = null;
TCCLink.prototype.ExtEvent_OnQueueStateChange = null;
TCCLink.prototype.QueueIndexByCode = function (QueueCode) {
    var index = -1;
    for (var fori = 0; fori < this.CCLinkQueueList.length; fori++) {
        if (this.CCLinkQueueList[fori].QueueCode == QueueCode) {
            index = fori;
            break;
        }
    }
    return index;
}
TCCLink.prototype.Queue_GetCodeList = function (Separator) {
    sQueueCodeList = "";
    if (this.CCLinkState != 0) {
        for (var fori = 0; fori < this.CCLinkQueueList.length; fori++) {
            if (sQueueCodeList == "") {
                sQueueCodeList = this.CCLinkQueueList[fori].QueueCode;
            }
            else {
                sQueueCodeList = sQueueCodeList + Separator + this.CCLinkQueueList[fori].QueueCode;
            }
        }
    }
    return sQueueCodeList;
}
TCCLink.prototype.Queue_Assign = function (QueueCode) {
    if (this.CCLinkState == 0) {
        return "服务器未连接!";
    }
    if (QueueCode == "*") {
        for (var fori = 0; fori < this.CCLinkQueueList.length; fori++) {
            this.CCLinkQueueList[fori].Assigned = 1;
        }
        var data = "?Action=" + encodeURIComponent("Queue_Assign")
                 + "&ActionTime=" + encodeURIComponent((new Date).getTime())
                 + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
                 + "&QueueCode=" + encodeURIComponent(QueueCode);
        CCLinkActionRequest(data);
    }
    else {
        var QueueIndex = this.QueueIndexByCode(QueueCode);
        if (QueueIndex == -1) {
            return "队列号错误！";
        }
        this.CCLinkQueueList[QueueIndex].Assigned = 1;
        var data = "?Action=" + encodeURIComponent("Queue_Assign")
                 + "&ActionTime=" + encodeURIComponent((new Date).getTime())
                 + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
                 + "&QueueCode=" + encodeURIComponent(QueueCode);
        CCLinkActionRequest(data);
    }
    return "";
}
TCCLink.prototype.Queue_Dassign = function (QueueCode) {
    if (this.CCLinkState == 0) {
        return;
    }
    if (QueueCode == "*") {
        for (var fori = 0; fori < this.CCLinkQueueList.length; fori++) {
            this.CCLinkQueueList[fori].Assigned = 0;
        }
        var data = "?Action=" + encodeURIComponent("Queue_Deassign")
                 + "&ActionTime=" + encodeURIComponent((new Date).getTime())
                 + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
                 + "&QueueCode=" + encodeURIComponent(QueueCode);
        CCLinkActionRequest(data);
    }
    else {
        var QueueIndex = this.QueueIndexByCode(QueueCode);
        if (QueueIndex != -1) {
            this.CCLinkQueueList[QueueIndex].Assigned = 0;
        }
        var data = "?Action=" + encodeURIComponent("Queue_Deassign")
                 + "&ActionTime=" + encodeURIComponent((new Date).getTime())
                 + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
                 + "&QueueCode=" + encodeURIComponent(QueueCode);
        CCLinkActionRequest(data);
    }
}
TCCLink.prototype.Queue_GetQueueName = function (QueueCode) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var QueueIndex = this.QueueIndexByCode(QueueCode);
        if (QueueIndex != -1) {
            sResult = this.CCLinkQueueList[QueueIndex].QueueName;
        }
    }
    return sResult;
}
TCCLink.prototype.Queue_GetExtCount = function (QueueCode) {
    var iResult = 0;
    if (this.CCLinkState != 0) {
        var QueueIndex = this.QueueIndexByCode(QueueCode);
        if (QueueIndex != -1) {
            iResult = this.CCLinkQueueList[QueueIndex].ExtList.length;
        }
    }
    return iResult;
}
TCCLink.prototype.Queue_GetExtCodeList = function (QueueCode, Separator) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var QueueIndex = this.QueueIndexByCode(QueueCode);
        if (QueueIndex != -1) {
            for (var fori = 0; fori < this.CCLinkQueueList[QueueIndex].ExtList.length; fori++) {
                if (sResult == "") {
                    sResult = this.CCLinkQueueList[QueueIndex].ExtList[fori].ExtCode;
                }
                else {
                    sResult = sResult + Separator + this.CCLinkQueueList[QueueIndex].ExtList[fori].ExtCode;
                }
            }

        }
    }
    return sResult;
}
TCCLink.prototype.Queue_GetExtInfo_Pri = function (QueueCode, ExtCode) {
    var iResult = 0;
    if (this.CCLinkState != 0) {
        var QueueIndex = this.QueueIndexByCode(QueueCode);
        if (QueueIndex != -1) {
            for (var fori = 0; fori < this.CCLinkQueueList[QueueIndex].ExtList.length; fori++) {
                if (this.CCLinkQueueList[QueueIndex].ExtList[fori].ExtCode == ExtCode) {
                    iResult = this.CCLinkQueueList[QueueIndex].ExtList[fori].Pri;
                    break;
                }
            }

        }
    }
    return iResult;
}
TCCLink.prototype.Queue_GetUserCount = function (QueueCode) {
    var iResult = 0;
    if (this.CCLinkState != 0) {
        var QueueIndex = this.QueueIndexByCode(QueueCode);
        if (QueueIndex != -1) {
            iResult = this.CCLinkQueueList[QueueIndex].UserList.length;
        }
    }
    return iResult;
}
TCCLink.prototype.Queue_GetUserIDList = function (QueueCode, Separator) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var QueueIndex = this.QueueIndexByCode(QueueCode);
        if (QueueIndex != -1) {
            for (var fori = 0; fori < this.CCLinkQueueList[QueueIndex].UserList.length; fori++) {
                if (sResult == "") {
                    sResult = this.CCLinkQueueList[QueueIndex].UserList[fori].UserID;
                }
                else {
                    sResult = sResult + Separator + this.CCLinkQueueList[QueueIndex].UserList[fori].UserID;
                }
            }

        }
    }
    return sResult;
}
TCCLink.prototype.Queue_GetUserInfo_Code = function (QueueCode, UserID) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var QueueIndex = this.QueueIndexByCode(QueueCode);
        if (QueueIndex != -1) {
            for (var fori = 0; fori < CCLinkQueueList[QueueIndex].UserList.length; fori++) {
                if (this.CCLinkQueueList[QueueIndex].UserList[fori].UserID == UserID) {
                    sResult = this.CCLinkQueueList[QueueIndex].UserList[fori].UserCode;
                    break;
                }
            }

        }
    }
    return sResult;
}
TCCLink.prototype.Queue_GetUserInfo_Role = function (QueueCode, UserID) {
    var iResult = 0;
    if (this.CCLinkState != 0) {
        var QueueIndex = this.QueueIndexByCode(QueueCode);
        if (QueueIndex != -1) {
            for (var fori = 0; fori < CCLinkQueueList[QueueIndex].UserList.length; fori++) {
                if (this.CCLinkQueueList[QueueIndex].UserList[fori].UserID == UserID) {
                    iResult = this.CCLinkQueueList[QueueIndex].UserList[fori].UserRole;
                    break;
                }
            }

        }
    }
    return sResult;
}
TCCLink.prototype.Queue_GetUserInfo_EnterTime = function (QueueCode, UserID) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var QueueIndex = this.QueueIndexByCode(QueueCode);
        if (QueueIndex != -1) {
            for (var fori = 0; fori < CCLinkQueueList[QueueIndex].UserList.length; fori++) {
                if (this.CCLinkQueueList[QueueIndex].UserList[fori].UserID == UserID) {
                    sResult = this.CCLinkQueueList[QueueIndex].UserList[fori].UserEnterTime;
                    break;
                }
            }

        }
    }
    return sResult;
}
TCCLink.prototype.QueueEvent_OnAssigned = null;
TCCLink.prototype.QueueEvent_OnAddUser = null;
TCCLink.prototype.QueueEvent_OnSubUser = null;
TCCLink.prototype.QueueEvent_OnAddExt = null;
TCCLink.prototype.QueueEvent_OnSubExt = null;
TCCLink.prototype.MeetingRoomIndexByCode = function (MeetingRoomCode) {
    var index = -1;
    for (var fori = 0; fori < this.CCLinkMeetingRoomList.length; fori++) {
        if (this.CCLinkMeetingRoomList[fori].MeetingRoomCode == MeetingRoomCode) {
            index = fori;
            break;
        }
    }
    return index;
}
TCCLink.prototype.MeetingRoom_GetCodeList = function (Separator) {
    sMeetingRoomCodeList = "";
    if (this.CCLinkState != 0) {
        for (var fori = 0; fori < this.CCLinkMeetingRoomList.length; fori++) {
            if (sMeetingRoomCodeList == "") {
                sMeetingRoomCodeList = this.CCLinkMeetingRoomList[fori].MeetingRoomCode;
            }
            else {
                sMeetingRoomCodeList = sMeetingRoomCodeList + Separator + this.CCLinkMeetingRoomList[fori].MeetingRoomCode;
            }
        }
    }
    return sMeetingRoomCodeList;
}
TCCLink.prototype.MeetingRoom_Assign = function (MeetingRoomCode, MeetingRoomPassword) {
    if (this.CCLinkState == 0) {
        return "服务器未连接!";
    }
    if (MeetingRoomCode == "*" && MeetingRoomPassword == "*") {
        for (var fori = 0; fori < this.CCLinkMeetingRoomList.length; fori++) {
            this.CCLinkMeetingRoomList[fori].Assigned = 1;
        }
        var data = "?Action=" + encodeURIComponent("MeetingRoom_Assign")
                 + "&ActionTime=" + encodeURIComponent((new Date).getTime())
                 + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
                 + "&MeetingRoomCode=" + encodeURIComponent(MeetingRoomCode)
                 + "&MeetingRoomPassword=" + encodeURIComponent(MeetingRoomPassword);
        CCLinkActionRequest(data);
    }
    else {
        var MeetingRoomIndex = this.MeetingRoomIndexByCode(MeetingRoomCode);
        if (MeetingRoomIndex == -1) {
            return "会议室号错误！";
        }
        if (MeetingRoomPassword != this.CCLinkMeetingRoomList[MeetingRoomIndex].MeetingRoomPassWD) {
            //return "会议室密码错误！";
        }
        this.CCLinkMeetingRoomList[MeetingRoomIndex].Assigned = 1;
        var data = "?Action=" + encodeURIComponent("MeetingRoom_Assign")
                 + "&ActionTime=" + encodeURIComponent((new Date).getTime())
                 + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
                 + "&MeetingRoomCode=" + encodeURIComponent(MeetingRoomCode)
                 + "&MeetingRoomPassword=" + encodeURIComponent(MeetingRoomPassword);
        CCLinkActionRequest(data);
    }
    return "";
}
TCCLink.prototype.MeetingRoom_Deassign = function (MeetingRoomCode) {
    if (this.CCLinkState == 0) {
        return;
    }
    if (MeetingRoomCode == "*") {
        for (var fori = 0; fori < this.CCLinkMeetingRoomList.length; fori++) {
            this.CCLinkMeetingRoomList[fori].Assigned = 0;
        }
        var data = "?Action=" + encodeURIComponent("MeetingRoom_Deassign")
                 + "&ActionTime=" + encodeURIComponent((new Date).getTime())
                 + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
                 + "&MeetingRoomCode=" + encodeURIComponent(MeetingRoomCode);
        CCLinkActionRequest(data);
    }
    else {
        var MeetingRoomIndex = this.MeetingRoomIndexByCode(MeetingRoomCode);
        if (MeetingRoomIndex != -1) {
            this.CCLinkMeetingRoomList[MeetingRoomIndex].Assigned = 0;
        }
        var data = "?Action=" + encodeURIComponent("MeetingRoom_Deassign")
                 + "&ActionTime=" + encodeURIComponent((new Date).getTime())
                 + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
                 + "&MeetingRoomCode=" + encodeURIComponent(MeetingRoomCode);
        CCLinkActionRequest(data);
    }
}
TCCLink.prototype.MeetingRoom_GetMeetingRoomName = function (MeetingRoomCode) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var MeetingRoomIndex = this.MeetingRoomIndexByCode(MeetingRoomCode);
        if (MeetingRoomIndex != -1) {
            sResult = this.CCLinkMeetingRoomList[MeetingRoomIndex].MeetingRoomName;
        }
    }
    return sResult;
}
TCCLink.prototype.MeetingRoom_GetMeetingRoomMode = function (MeetingRoomCode) {
    var iResult = 0;
    if (this.CCLinkState != 0) {
        var MeetingRoomIndex = this.MeetingRoomIndexByCode(MeetingRoomCode);
        if (MeetingRoomIndex != -1) {
            iResult = this.CCLinkMeetingRoomList[MeetingRoomIndex].MeetingRoomMode;
        }
    }
    return iResult;
}
TCCLink.prototype.MeetingRoom_GetMeetingRoomMemberIDList = function (MeetingRoomCode, Separator) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var MeetingRoomIndex = this.MeetingRoomIndexByCode(MeetingRoomCode);
        if (MeetingRoomIndex != -1) {
            for (var fori = 0; fori < this.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList.length; fori++) {
                if (sResult == "") {
                    sResult = this.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList[fori].MemberID;
                }
                else {
                    sResult = sResult + Separator + this.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList[fori].MemberID;
                }
            }

        }
    }
    return sResult;
}
TCCLink.prototype.MeetingRoom_GetMeetingRoomInfo_MemberCode = function (MeetingRoomCode, MemberID) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var MeetingRoomIndex = this.MeetingRoomIndexByCode(MeetingRoomCode);
        if (MeetingRoomIndex != -1) {
            for (var fori = 0; fori < CCLinkMeetingRoomList[MeetingRoomIndex].MemberList.length; fori++) {
                if (this.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList[fori].MemberID == MemberID) {
                    sResult = this.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList[fori].MemberCode;
                    break;
                }
            }

        }
    }
    return sResult;
}
TCCLink.prototype.MeetingRoom_GetMeetingRoomInfo_MemberType = function (MeetingRoomCode, MemberID) {
    var iResult = 0;
    if (this.CCLinkState != 0) {
        var MeetingRoomIndex = this.MeetingRoomIndexByCode(MeetingRoomCode);
        if (MeetingRoomIndex != -1) {
            for (var fori = 0; fori < CCLinkMeetingRoomList[MeetingRoomIndex].MemberList.length; fori++) {
                if (this.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList[fori].MemberID == MemberID) {
                    iResult = this.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList[fori].MemberType;
                    break;
                }
            }

        }
    }
    return iResult;
}
TCCLink.prototype.MeetingRoom_GetMeetingRoomInfo_MemberRole = function (MeetingRoomCode, MemberID) {
    var iResult = 0;
    if (this.CCLinkState != 0) {
        var MeetingRoomIndex = this.MeetingRoomIndexByCode(MeetingRoomCode);
        if (MeetingRoomIndex != -1) {
            for (var fori = 0; fori < CCLinkMeetingRoomList[MeetingRoomIndex].MemberList.length; fori++) {
                if (this.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList[fori].MemberID == MemberID) {
                    iResult = this.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList[fori].MemberRole;
                    break;
                }
            }

        }
    }
    return iResult;
}
TCCLink.prototype.MeetingRoom_GetMeetingRoomInfo_EnterTime = function (MeetingRoomCode, MemberID) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var MeetingRoomIndex = this.MeetingRoomIndexByCode(MeetingRoomCode);
        if (MeetingRoomIndex != -1) {
            for (var fori = 0; fori < CCLinkMeetingRoomList[MeetingRoomIndex].MemberList.length; fori++) {
                if (this.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList[fori].MemberID == MemberID) {
                    sResult = this.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList[fori].EnterTime;
                    break;
                }
            }

        }
    }
    return sResult;
}
TCCLink.prototype.MeetingRoom_MakeCall = function (MeetingRoomCode, DestCode, ConfMode, secTimeOut) {
    if (this.CCLinkState == 0) {
        return;
    }
    var data = "?Action=" + encodeURIComponent("MeetingRoom_MakeCall")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&MeetingRoomCode=" + encodeURIComponent(MeetingRoomCode)
             + "&DestCode=" + encodeURIComponent(DestCode)
             + "&ConfMode=" + encodeURIComponent(ConfMode)
             + "&secTimeOut=" + encodeURIComponent(secTimeOut);
    CCLinkActionRequest(data);
}
TCCLink.prototype.MeetingRoom_ClearMember = function (MeetingRoomCode) {
    if (this.CCLinkState == 0) {
        return;
    }
    var data = "?Action=" + encodeURIComponent("MeetingRoom_ClearMember")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&MeetingRoomCode=" + encodeURIComponent(MeetingRoomCode);
    CCLinkActionRequest(data);
}
TCCLink.prototype.MeetingRoom_DeleteMember = function (MeetingRoomCode, MemberID) {
    if (this.CCLinkState == 0) {
        return;
    }
    var data = "?Action=" + encodeURIComponent("MeetingRoom_DeleteMember")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&MeetingRoomCode=" + encodeURIComponent(MeetingRoomCode)
             + "&MemberID=" + encodeURIComponent(MemberID);
    CCLinkActionRequest(data);
}
TCCLink.prototype.MeetingRoom_LetSpeak = function (MeetingRoomCode, MemberID) {
    if (this.CCLinkState == 0) {
        return;
    }
    var data = "?Action=" + encodeURIComponent("MeetingRoom_LetSpeak")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&MeetingRoomCode=" + encodeURIComponent(MeetingRoomCode)
             + "&MemberID=" + encodeURIComponent(MemberID);
    CCLinkActionRequest(data);
}
TCCLink.prototype.MeetingRoom_StopSpeak = function (MeetingRoomCode, MemberID) {
    if (this.CCLinkState == 0) {
        return;
    }
    var data = "?Action=" + encodeURIComponent("MeetingRoom_StopSpeak")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&MeetingRoomCode=" + encodeURIComponent(MeetingRoomCode)
             + "&MemberID=" + encodeURIComponent(MemberID);
    CCLinkActionRequest(data);
}
TCCLink.prototype.MeetingRoom_MoveMember = function (MeetingRoomCode, MemberID, NewMeetingRoomCode) {
    if (this.CCLinkState == 0) {
        return;
    }
    var data = "?Action=" + encodeURIComponent("MeetingRoom_MoveMember")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&MeetingRoomCode=" + encodeURIComponent(MeetingRoomCode)
             + "&MemberID=" + encodeURIComponent(MemberID)
             + "&NewMeetingRoomCode=" + encodeURIComponent(NewMeetingRoomCode);
    CCLinkActionRequest(data);
}
TCCLink.prototype.MeetingRoomEvent_OnAddMember = null;
TCCLink.prototype.MeetingRoomEvent_OnDeleteMember = null;
TCCLink.prototype.MeetingRoomEvent_OnMemberTypeChange = null;
TCCLink.prototype.MeetingRoomEvent_OnAddRoom = null;
TCCLink.prototype.MeetingRoomEvent_OnDeleteRoom = null;
TCCLink.prototype.TrunkIndexByChannelNo = function (ChannelNo) {
    var index = -1;
    for (var fori = 0; fori < this.CCLinkTrunkList.length; fori++) {
        if (this.CCLinkTrunkList[fori].ChannelNo == ChannelNo) {
            index = fori;
            break;
        }
    }
    return index;
}
TCCLink.prototype.Trunk_GetChannelNoList = function (Separator) {
    var sChannelNoList = "";
    if (this.CCLinkState != 0) {
        for (var fori = 0; fori < this.CCLinkTrunkList.length; fori++) {
            if (fori == 0) {
                sChannelNoList = this.CCLinkTrunkList[fori].ChannelNo;
            }
            else {
                sChannelNoList = sChannelNoList + Separator + this.CCLinkTrunkList[fori].ChannelNo;
            }
        }
    }
    return sChannelNoList;
}
TCCLink.prototype.Trunk_Assign = function (ChannelNo) {
    if (this.CCLinkState == 0) {
        return "服务器未连接!";
    }
    if (ChannelNo == -1) {
        for (var fori = 0; fori < this.CCLinkTrunkList.length; fori++) {
            this.CCLinkTrunkList[fori].Assigned = 1;
        }
        var data = "?Action=" + encodeURIComponent("Trunk_Assign")
                 + "&ActionTime=" + encodeURIComponent((new Date).getTime())
                 + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
                 + "&ChannelNo=" + encodeURIComponent(ChannelNo);
        CCLinkActionRequest(data);
    }
    else {
        var TrunkIndex = this.TrunkIndexByChannelNo(ChannelNo);
        if (TrunkIndex == -1) {
            return "中继通道号错误！";
        }
        this.CCLinkTrunkList[TrunkIndex].Assigned = 1;
        var data = "?Action=" + encodeURIComponent("Trunk_Assign")
                 + "&ActionTime=" + encodeURIComponent((new Date).getTime())
                 + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
                 + "&ChannelNo=" + encodeURIComponent(ChannelNo);
        CCLinkActionRequest(data);
    }
    return "";
}
TCCLink.prototype.Trunk_Deassign = function (ChannelNo) {
    if (this.CCLinkState == 0) {
        return;
    }
    if (ChannelNo == -1) {
        for (var fori = 0; fori < this.CCLinkTrunkList.length; fori++) {
            this.CCLinkTrunkList[fori].Assigned = 0;
        }
        var data = "?Action=" + encodeURIComponent("Trunk_Deassign")
                 + "&ActionTime=" + encodeURIComponent((new Date).getTime())
                 + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
                 + "&ChannelNo=" + encodeURIComponent(ChannelNo);
        CCLinkActionRequest(data);
    }
    else {
        var TrunkIndex = this.TrunkIndexByChannelNo(ChannelNo);
        if (TrunkIndex != -1) {
            this.CCLinkTrunkList[TrunkIndex].Assigned = 0;
        }
        var data = "?Action=" + encodeURIComponent("Trunk_Deassign")
                 + "&ActionTime=" + encodeURIComponent((new Date).getTime())
                 + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
                 + "&ChannelNo=" + encodeURIComponent(ChannelNo);
        CCLinkActionRequest(data);
    }
}
TCCLink.prototype.Trunk_GetChannelState = function (ChannelNo) {
    var iResult = 0;
    if (this.CCLinkState != 0) {
        var TrunkIndex = this.TrunkIndexByChannelNo(ChannelNo);
        if (TrunkIndex != -1) {
            iResult = this.CCLinkTrunkList[TrunkIndex].CallState;
        }
    }
    return iResult;
}
TCCLink.prototype.Trunk_GetChannelInfo = function (ChannelNo) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var TrunkIndex = this.TrunkIndexByChannelNo(ChannelNo);
        if (TrunkIndex != -1) {
            sResult = this.CCLinkTrunkList[TrunkIndex].ChannelInfo;
        }
    }
    return sResult;
}
TCCLink.prototype.Trunk_GetChannelCode = function (ChannelNo) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var TrunkIndex = this.TrunkIndexByChannelNo(ChannelNo);
        if (TrunkIndex != -1) {
            sResult = this.CCLinkTrunkList[TrunkIndex].ChannelCode;
        }
    }
    return sResult;
}
TCCLink.prototype.Trunk_GetChannelRole = function (ChannelNo) {
    var iResult = 0;
    if (this.CCLinkState != 0) {
        var TrunkIndex = this.TrunkIndexByChannelNo(ChannelNo);
        if (TrunkIndex != -1) {
            iResult = this.CCLinkTrunkList[TrunkIndex].ChannelRole;
        }
    }
    return iResult;
}
TCCLink.prototype.Trunk_GetCallSessionID = function (ChannelNo) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var TrunkIndex = this.TrunkIndexByChannelNo(ChannelNo);
        if (TrunkIndex != -1) {
            sResult = this.CCLinkTrunkList[TrunkIndex].CallSessionID;
        }
    }
    return sResult;
}
TCCLink.prototype.Trunk_CallDirect = function (ChannelNo) {
    var iResult = 0;
    if (this.CCLinkState != 0) {
        var TrunkIndex = this.TrunkIndexByChannelNo(ChannelNo);
        if (TrunkIndex != -1) {
            iResult = this.CCLinkTrunkList[TrunkIndex].CallDirect;
        }
    }
    return iResult;
}
TCCLink.prototype.Trunk_GetANI = function (ChannelNo) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var TrunkIndex = this.TrunkIndexByChannelNo(ChannelNo);
        if (TrunkIndex != -1) {
            sResult = this.CCLinkTrunkList[TrunkIndex].ANI;
        }
    }
    return sResult;
}
TCCLink.prototype.Trunk_GetDNIS = function (ChannelNo) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var TrunkIndex = this.TrunkIndexByChannelNo(ChannelNo);
        if (TrunkIndex != -1) {
            sResult = this.CCLinkTrunkList[TrunkIndex].DNIS;
        }
    }
    return sResult;
}
TCCLink.prototype.Trunk_GetCallTime = function (ChannelNo) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var TrunkIndex = this.TrunkIndexByChannelNo(ChannelNo);
        if (TrunkIndex != -1) {
            sResult = this.CCLinkTrunkList[TrunkIndex].CallTime;
        }
    }
    return sResult;
}
TCCLink.prototype.Trunk_GetConnectTime = function (ChannelNo) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var TrunkIndex = this.TrunkIndexByChannelNo(ChannelNo);
        if (TrunkIndex != -1) {
            sResult = this.CCLinkTrunkList[TrunkIndex].ConnectTime;
        }
    }
    return sResult;
}
TCCLink.prototype.Trunk_GetOtherCode = function (ChannelNo) {
    var sResult = "";
    if (this.CCLinkState != 0) {
        var TrunkIndex = this.TrunkIndexByChannelNo(ChannelNo);
        if (TrunkIndex != -1) {
            sResult = this.CCLinkTrunkList[TrunkIndex].OtherCode;
        }
    }
    return sResult;
}
TCCLink.prototype.Trunk_GetOtherRole = function (ChannelNo) {
    var iResult = 0;
    if (this.CCLinkState != 0) {
        var TrunkIndex = this.TrunkIndexByChannelNo(ChannelNo);
        if (TrunkIndex != -1) {
            iResult = this.CCLinkTrunkList[TrunkIndex].OtherRole;
        }
    }
    return iResult;
}
TCCLink.prototype.TrunkEvent_OnCallStateChange = null;
TCCLink.prototype.TrunkEvent_OnChannelInfoChange = null;
TCCLink.prototype.SIPSoftPhone_Register = function (SIPServerIP, SIPServerPort, ExtCode, ExtPasswd) {
    if (this.CCLinkState == 0) {
        return;
    }
    var data = "?Action=" + encodeURIComponent("SIPSoftPhone_Register")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&SIPServerIP=" + encodeURIComponent(SIPServerIP)
             + "&SIPServerPort=" + encodeURIComponent(SIPServerPort)
             + "&ExtCode=" + encodeURIComponent(ExtCode)
             + "&ExtPasswd=" + encodeURIComponent(ExtPasswd);
    CCLinkActionRequest(data);
}
TCCLink.prototype.SIPSoftPhoneEvent_OnRegister = null;
TCCLink.prototype.SIPSoftPhoneEvent_OnCallIn = null;
TCCLink.prototype.Other_ExtensionToolBar = function (ExtCode, Buttons, Visiabled) {
    if (this.CCLinkState == 0) {
        return;
    }
    var data = "?Action=" + encodeURIComponent("Other_ExtensionToolBar")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&ExtCode=" + encodeURIComponent(ExtCode)
             + "&Buttons=" + encodeURIComponent(Buttons)
             + "&Visiabled=" + encodeURIComponent(Visiabled);
    CCLinkActionRequest(data);
}

TCCLink.prototype.Other_AgentMonitor = function (CCServerHost, CCServerPort, ExtCode, DNDAlertTimeLen, PopMenuEnabled, QueueVisible, AgentVisible, TrunkVisible, WorkNoList, ExtCodeList, QueueCodeList, TrunkAcceptCodeList, HidePhoneNumber) {
    var Url = this.CCLinkUrl;
    if (Url == "") {
        Url = "http://127.0.0.1:3008/";
    }
    var data = "?Action=" + encodeURIComponent("Other_AgentMonitor")
             + "&ActionTime=" + encodeURIComponent((new Date).getTime())
             + "&SessionID=" + encodeURIComponent(this.CCLinkSessionID)
             + "&CCServerHost=" + encodeURIComponent(CCServerHost)
             + "&CCServerPort=" + encodeURIComponent(CCServerPort)
             + "&ExtCode=" + encodeURIComponent(ExtCode)
             + "&DNDAlertTimeLen=" + encodeURIComponent(DNDAlertTimeLen)
             + "&PopMenuEnabled=" + encodeURIComponent(PopMenuEnabled)
             + "&QueueVisible=" + encodeURIComponent(QueueVisible)
             + "&AgentVisible=" + encodeURIComponent(AgentVisible)
             + "&TrunkVisible=" + encodeURIComponent(TrunkVisible)
             + "&WorkNoList=" + encodeURIComponent(WorkNoList)
             + "&ExtCodeList=" + encodeURIComponent(ExtCodeList)
             + "&QueueCodeList=" + encodeURIComponent(QueueCodeList)
             + "&TrunkAcceptCodeList=" + encodeURIComponent(TrunkAcceptCodeList)
             + "&HidePhoneNumber=" + encodeURIComponent(HidePhoneNumber);
    var head = document.getElementsByTagName("head")[0] || document.documentElement;
    var script = document.createElement("script");
    script.type = "text/javascript";
    script.src = Url + data;
    script.charset = "gb2312";
    var done = false;
    script.onerror = script.onload = script.onreadystatechange = function () {
        if (!done && (!this.readyState || this.readyState === "loaded" || this.readyState === "complete")) {
            done = true;
            if (script) {
                script.onload = script.onreadystatechange = null;
                if (head && script.parentNode) {
                    head.removeChild(script);
                }
            }
        }
    }
    head.insertBefore(script, head.firstChild);
}

var CCLink = new TCCLink();

String.prototype.replaceAll = function (reallyDo, replaceWith, ignoreCase) {
    if (!RegExp.prototype.isPrototypeOf(reallyDo)) {
        return this.replace(new RegExp(reallyDo, (ignoreCase ? "gi" : "g")), replaceWith);
    } else {
        return this.replace(reallyDo, replaceWith);
    }
}

function CCLinkActionRequest(RequestData) {
    var head = document.getElementsByTagName("head")[0] || document.documentElement;
    var script = document.createElement("script");
    script.type = "text/javascript";
    if (CCLink.CCLinkUrl) {
        script.src = CCLink.CCLinkUrl + RequestData;
    }
    else {
        SessionIDString = "SessionID=" + encodeURIComponent(CCLink.CCLinkSessionID);
        iStart = RequestData.indexOf(SessionIDString);
        iEnd = iStart + SessionIDString.length;
        if (iStart >= 0) {
            RequestData = RequestData.substring(0, iStart) + "&SessionID=" + encodeURIComponent("CCLink2008") + RequestData.substring(iEnd);
        }
        script.src = "http://" + CCLink.CCLinkDefaultHost + ":" + CCLink.CCLinkDefaultPort + "/" + RequestData;
    }
    script.charset = "gb2312";
    var done = false;
    script.onerror = script.onload = script.onreadystatechange = function () {
        if (!done && (!this.readyState || this.readyState === "loaded" || this.readyState === "complete")) {
            done = true;
            if (script) {
                script.onload = script.onreadystatechange = null;
                if (head && script.parentNode) {
                    head.removeChild(script);
                    if (CCLink.CCLinkState == 0) {
                        if ((CCLink.LinkEvent_OnDisconnected) && (CCLink.CCLinkUrl != "")) {
                            CCLink.LinkEvent_OnDisconnected.call(CCLink);
                        }
                        CCLink.CCLinkUrl = "";
                    }
                }
            }
        }
    }
    head.insertBefore(script, head.firstChild);
}
function CCLinkHeartBeat() {
    var now = new Date();
    if (CCLink.CCLinkState != 0) {
        var data = "?Action=" + encodeURIComponent("HeartBeat")
                 + "&ActionTime=" + encodeURIComponent((new Date).getTime())
                 + "&SessionID=" + encodeURIComponent(CCLink.CCLinkSessionID)
                 + "&EventID=" + encodeURIComponent(CCLink.CCLinkEventID);
        if ((now - CCLink.CCLinkRefreshTime) / 1000 >= 1800) {
            data = "?Action=" + encodeURIComponent("Link_Refresh")
                 + "&ActionTime=" + encodeURIComponent((new Date).getTime())
                 + "&SessionID=" + encodeURIComponent(CCLink.CCLinkSessionID)
                 + "&EventID=" + encodeURIComponent(CCLink.CCLinkEventID);
        }
        var head = document.getElementsByTagName("head")[0] || document.documentElement;
        var script = document.createElement("script");
        script.type = "text/javascript";
        script.src = CCLink.CCLinkUrl + data;
        script.charset = "gb2312";
        var done = false;
        script.onerror = script.onload = script.onreadystatechange = function () {
            if (!done && (!this.readyState || this.readyState === "loaded" || this.readyState === "complete")) {
                done = true;
                script.onload = script.onreadystatechange = null;
                if (head && script.parentNode) {
                    head.removeChild(script);
                    var now = new Date();
                    if (((now - CCLink.CCLinkInvokeFailTime) / 1000 >= 30) || (CCLink.CCLinkState == 0)) {
                        CCLink.CCLinkState = 0;
                        if ((CCLink.LinkEvent_OnDisconnected) && (CCLink.CCLinkUrl != "")) {
                            CCLink.LinkEvent_OnDisconnected.call(CCLink);
                        }
                        CCLink.CCLinkUrl = "";
                    }
                }
                if (CCLink.CCLinkState == 1) {
                    setTimeout("CCLinkHeartBeat();", 500);
                }
            }
        }
        head.insertBefore(script, head.firstChild);
    }
}
function CCLinkCallBack(jsonp) {
    for (var eventIndex = 0; eventIndex < jsonp.length; eventIndex++) {
        eventJson = jsonp[eventIndex];
        if ((eventJson.EventID > CCLink.CCLinkEventID) || (eventJson.EventID == 0)) {
            if (eventJson.Action == "LinkEvent_OnRefresh") {
                for (var fori = 0; fori < eventJson.ExtList.length; fori++) {
                    CCLink.CCLinkExtList[fori].ExtCode = eventJson.ExtList[fori].ExtCode;
                    CCLink.CCLinkExtList[fori].ExtPassWD = eventJson.ExtList[fori].ExtPassWD;
                    CCLink.CCLinkExtList[fori].ExtWorkNo = eventJson.ExtList[fori].ExtWorkNo;
                    CCLink.CCLinkExtList[fori].ExtName = eventJson.ExtList[fori].ExtName;
                    CCLink.CCLinkExtList[fori].ExtCaller = eventJson.ExtList[fori].ExtCaller;
                    CCLink.CCLinkExtList[fori].ExtType = eventJson.ExtList[fori].ExtType;
                    CCLink.CCLinkExtList[fori].CallState = eventJson.ExtList[fori].CallState;
                    CCLink.CCLinkExtList[fori].CallDirect = eventJson.ExtList[fori].CallDirect;
                    CCLink.CCLinkExtList[fori].ExtDoNotDisturbTime = eventJson.ExtList[fori].ExtDoNotDisturbTime;
                    CCLink.CCLinkExtList[fori].ExtDoNotDisturb = eventJson.ExtList[fori].ExtDoNotDisturb;
                    CCLink.CCLinkExtList[fori].LoginMode = eventJson.ExtList[fori].LoginMode;
                    CCLink.CCLinkExtList[fori].LastCallResult = eventJson.ExtList[fori].LastCallResult;
                    CCLink.CCLinkExtList[fori].ANI = eventJson.ExtList[fori].ANI;
                    CCLink.CCLinkExtList[fori].DNIS = eventJson.ExtList[fori].DNIS;
                    CCLink.CCLinkExtList[fori].ChannelData = eventJson.ExtList[fori].ChannelData;
                    CCLink.CCLinkExtList[fori].OtherANI = eventJson.ExtList[fori].OtherANI;
                    CCLink.CCLinkExtList[fori].OtherDNIS = eventJson.ExtList[fori].OtherDNIS;
                    CCLink.CCLinkExtList[fori].OtherChannelData = eventJson.ExtList[fori].OtherChannelData;
                    CCLink.CCLinkExtList[fori].CallSessionID = eventJson.ExtList[fori].CallSessionID;
                    CCLink.CCLinkExtList[fori].CallIndex = eventJson.ExtList[fori].CallIndex;
                    CCLink.CCLinkExtList[fori].CallTime = eventJson.ExtList[fori].CallTime;
                    CCLink.CCLinkExtList[fori].ConnectTime = eventJson.ExtList[fori].ConnectTime;
                    CCLink.CCLinkExtList[fori].RecordFile = eventJson.ExtList[fori].RecordFile;
                    CCLink.CCLinkExtList[fori].OtherCode = eventJson.ExtList[fori].OtherCode;
                    CCLink.CCLinkExtList[fori].OtherRole = eventJson.ExtList[fori].OtherRole;
                    CCLink.CCLinkExtList[fori].TransferCode = eventJson.ExtList[fori].TransferCode;
                    CCLink.CCLinkExtList[fori].TransferRole = eventJson.ExtList[fori].TransferRole;
                    CCLink.CCLinkExtList[fori].CallData = eventJson.ExtList[fori].CallData;
                    CCLink.CCLinkExtList[fori].QueueState = eventJson.ExtList[fori].QueueState;
                }
                for (var fori = 0; fori < eventJson.QueueList.length; fori++) {
                    CCLink.CCLinkQueueList[fori].QueueCode = eventJson.QueueList[fori].QueueCode;
                    CCLink.CCLinkQueueList[fori].QueueName = eventJson.QueueList[fori].QueueName;
                    CCLink.CCLinkQueueList[fori].ExtList = new Array();
                    CCLink.CCLinkQueueList[fori].ExtList.length = 0;
                    for (var forj = 0; forj < eventJson.QueueList[fori].ExtList.length; forj++) {
                        CCLink.CCLinkQueueList[fori].ExtList[forj] = new TCCLinkQueueExt();
                        CCLink.CCLinkQueueList[fori].ExtList[forj].ExtCode = eventJson.QueueList[fori].ExtList[forj].ExtCode;
                        CCLink.CCLinkQueueList[fori].ExtList[forj].Pri = eventJson.QueueList[fori].ExtList[forj].Pri;
                    }
                    CCLink.CCLinkQueueList[fori].UserList = new Array();
                    CCLink.CCLinkQueueList[fori].UserList.length = 0;
                    for (var forj = 0; forj < eventJson.QueueList[fori].UserList.length; forj++) {
                        CCLink.CCLinkQueueList[fori].UserList[forj] = new TCCLinkQueueUser();
                        CCLink.CCLinkQueueList[fori].UserList[forj].UserID = eventJson.QueueList[fori].UserList[forj].UserID;
                        CCLink.CCLinkQueueList[fori].UserList[forj].UserCode = eventJson.QueueList[fori].UserList[forj].UserCode;
                        CCLink.CCLinkQueueList[fori].UserList[forj].UserRole = eventJson.QueueList[fori].UserList[forj].UserRole;
                        CCLink.CCLinkQueueList[fori].UserList[forj].UserEnterTime = eventJson.QueueList[fori].UserList[forj].UserEnterTime;
                    }
                }
                for (var fori = 0; fori < eventJson.MeetingRoomList.length; fori++) {
                    CCLink.CCLinkMeetingRoomList[fori].MeetingRoomCode = eventJson.MeetingRoomList[fori].MeetingRoomCode;
                    CCLink.CCLinkMeetingRoomList[fori].MeetingRoomPassWD = eventJson.MeetingRoomList[fori].MeetingRoomCode;
                    CCLink.CCLinkMeetingRoomList[fori].MeetingRoomName = eventJson.MeetingRoomList[fori].MeetingRoomName;
                    CCLink.CCLinkMeetingRoomList[fori].MeetingRoomMode = eventJson.MeetingRoomList[fori].MeetingRoomMode;
                    CCLink.CCLinkMeetingRoomList[fori].MemberList = new Array();
                    CCLink.CCLinkMeetingRoomList[fori].MemberList.length = 0;
                    for (var forj = 0; forj < eventJson.MeetingRoomList[fori].MemberList.length; forj++) {
                        CCLink.CCLinkMeetingRoomList[fori].MemberList[forj] = new TCCLinkMeetingRoomMember();
                        CCLink.CCLinkMeetingRoomList[fori].MemberList[forj].MemberID = eventJson.MeetingRoomList[fori].MemberList[forj].MemberID;
                        CCLink.CCLinkMeetingRoomList[fori].MemberList[forj].MemberCode = eventJson.MeetingRoomList[fori].MemberList[forj].MemberCode;
                        CCLink.CCLinkMeetingRoomList[fori].MemberList[forj].MemberRole = eventJson.MeetingRoomList[fori].MemberList[forj].MemberRole;
                        CCLink.CCLinkMeetingRoomList[fori].MemberList[forj].MemberType = eventJson.MeetingRoomList[fori].MemberList[forj].MemberType;
                        CCLink.CCLinkMeetingRoomList[fori].MemberList[forj].EnterTime = eventJson.MeetingRoomList[fori].MemberList[forj].EnterTime;
                        CCLink.CCLinkMeetingRoomList[fori].MemberList[forj].ChannelData = eventJson.MeetingRoomList[fori].MemberList[forj].ChannelData;
                    }
                }
                for (var fori = 0; fori < eventJson.TrunkList.length; fori++) {
                    CCLink.CCLinkTrunkList[fori].ChannelNo = eventJson.TrunkList[fori].ChannelNo;
                    CCLink.CCLinkTrunkList[fori].CallState = eventJson.TrunkList[fori].CallState;
                    CCLink.CCLinkTrunkList[fori].ChannelCode = eventJson.TrunkList[fori].ChannelCode;
                    CCLink.CCLinkTrunkList[fori].ChannelRole = eventJson.TrunkList[fori].ChannelRole;
                    CCLink.CCLinkTrunkList[fori].CallSessionID = eventJson.TrunkList[fori].CallSessionID;
                    CCLink.CCLinkTrunkList[fori].CallDirect = eventJson.TrunkList[fori].CallDirect;
                    CCLink.CCLinkTrunkList[fori].ANI = eventJson.TrunkList[fori].ANI;
                    CCLink.CCLinkTrunkList[fori].DNIS = eventJson.TrunkList[fori].DNIS;
                    CCLink.CCLinkTrunkList[fori].ChannelData = eventJson.TrunkList[fori].ChannelData;
                    CCLink.CCLinkTrunkList[fori].CallTime = eventJson.TrunkList[fori].CallTime;
                    CCLink.CCLinkTrunkList[fori].ConnectTime = eventJson.TrunkList[fori].ConnectTime;
                    CCLink.CCLinkTrunkList[fori].OtherCode = eventJson.TrunkList[fori].OtherCode;
                    CCLink.CCLinkTrunkList[fori].OtherRole = eventJson.TrunkList[fori].OtherRole;
                    CCLink.CCLinkTrunkList[fori].ChannelInfo = eventJson.TrunkList[fori].ChannelInfo;
                    CCLink.CCLinkTrunkList[fori].LogEnabled = eventJson.TrunkList[fori].LogEnabled;
                }
                CCLink.CCLinkEventID = 0;
                CCLink.CCLinkRefreshTime = new Date();
            }
            if (eventJson.Action == "LinkEvent_OnConnected") {
                if (eventJson.ClientIP) {
                    CCLink.CCLinkClientIP = eventJson.ClientIP;
                }
                CCLink.CCLinkExtList.length = 0;
                for (var fori = 0; fori < eventJson.ExtList.length; fori++) {
                    CCLink.CCLinkExtList[fori] = new TCCLinkExt();
                    CCLink.CCLinkExtList[fori].Assigned = 0;
                    CCLink.CCLinkExtList[fori].ExtCode = eventJson.ExtList[fori].ExtCode;
                    CCLink.CCLinkExtList[fori].ExtPassWD = eventJson.ExtList[fori].ExtPassWD;
                    CCLink.CCLinkExtList[fori].ExtWorkNo = eventJson.ExtList[fori].ExtWorkNo;
                    CCLink.CCLinkExtList[fori].ExtName = eventJson.ExtList[fori].ExtName;
                    CCLink.CCLinkExtList[fori].ExtCaller = eventJson.ExtList[fori].ExtCaller;
                    CCLink.CCLinkExtList[fori].ExtType = eventJson.ExtList[fori].ExtType;
                    CCLink.CCLinkExtList[fori].CallState = eventJson.ExtList[fori].CallState;
                    CCLink.CCLinkExtList[fori].CallDirect = eventJson.ExtList[fori].CallDirect;
                    CCLink.CCLinkExtList[fori].ExtDoNotDisturbTime = eventJson.ExtList[fori].ExtDoNotDisturbTime;
                    CCLink.CCLinkExtList[fori].ExtDoNotDisturb = eventJson.ExtList[fori].ExtDoNotDisturb;
                    CCLink.CCLinkExtList[fori].LoginMode = eventJson.ExtList[fori].LoginMode;
                    CCLink.CCLinkExtList[fori].LastCallResult = eventJson.ExtList[fori].LastCallResult;
                    CCLink.CCLinkExtList[fori].ANI = eventJson.ExtList[fori].ANI;
                    CCLink.CCLinkExtList[fori].DNIS = eventJson.ExtList[fori].DNIS;
                    CCLink.CCLinkExtList[fori].ChannelData = eventJson.ExtList[fori].ChannelData;
                    CCLink.CCLinkExtList[fori].OtherANI = eventJson.ExtList[fori].OtherANI;
                    CCLink.CCLinkExtList[fori].OtherDNIS = eventJson.ExtList[fori].OtherDNIS;
                    CCLink.CCLinkExtList[fori].OtherChannelData = eventJson.ExtList[fori].OtherChannelData;
                    CCLink.CCLinkExtList[fori].CallSessionID = eventJson.ExtList[fori].CallSessionID;
                    CCLink.CCLinkExtList[fori].CallIndex = eventJson.ExtList[fori].CallIndex;
                    CCLink.CCLinkExtList[fori].CallTime = eventJson.ExtList[fori].CallTime;
                    CCLink.CCLinkExtList[fori].ConnectTime = eventJson.ExtList[fori].ConnectTime;
                    CCLink.CCLinkExtList[fori].RecordFile = eventJson.ExtList[fori].RecordFile;
                    CCLink.CCLinkExtList[fori].OtherCode = eventJson.ExtList[fori].OtherCode;
                    CCLink.CCLinkExtList[fori].OtherRole = eventJson.ExtList[fori].OtherRole;
                    CCLink.CCLinkExtList[fori].TransferCode = eventJson.ExtList[fori].TransferCode;
                    CCLink.CCLinkExtList[fori].TransferRole = eventJson.ExtList[fori].TransferRole;
                    CCLink.CCLinkExtList[fori].CallData = eventJson.ExtList[fori].CallData;
                    CCLink.CCLinkExtList[fori].QueueState = eventJson.ExtList[fori].QueueState;
                }
                CCLink.CCLinkQueueList.length = 0;
                for (var fori = 0; fori < eventJson.QueueList.length; fori++) {
                    CCLink.CCLinkQueueList[fori] = new TCCLinkQueue();
                    CCLink.CCLinkQueueList[fori].Assigned = 0;
                    CCLink.CCLinkQueueList[fori].QueueCode = eventJson.QueueList[fori].QueueCode;
                    CCLink.CCLinkQueueList[fori].QueueName = eventJson.QueueList[fori].QueueName;
                    CCLink.CCLinkQueueList[fori].ExtList = new Array();
                    CCLink.CCLinkQueueList[fori].ExtList.length = 0;
                    for (var forj = 0; forj < eventJson.QueueList[fori].ExtList.length; forj++) {
                        CCLink.CCLinkQueueList[fori].ExtList[forj] = new TCCLinkQueueExt();
                        CCLink.CCLinkQueueList[fori].ExtList[forj].ExtCode = eventJson.QueueList[fori].ExtList[forj].ExtCode;
                        CCLink.CCLinkQueueList[fori].ExtList[forj].Pri = eventJson.QueueList[fori].ExtList[forj].Pri;
                    }
                    CCLink.CCLinkQueueList[fori].UserList = new Array();
                    CCLink.CCLinkQueueList[fori].UserList.length = 0;
                    for (var forj = 0; forj < eventJson.QueueList[fori].UserList.length; forj++) {
                        CCLink.CCLinkQueueList[fori].UserList[forj] = new TCCLinkQueueUser();
                        CCLink.CCLinkQueueList[fori].UserList[forj].UserID = eventJson.QueueList[fori].UserList[forj].UserID;
                        CCLink.CCLinkQueueList[fori].UserList[forj].UserCode = eventJson.QueueList[fori].UserList[forj].UserCode;
                        CCLink.CCLinkQueueList[fori].UserList[forj].UserRole = eventJson.QueueList[fori].UserList[forj].UserRole;
                        CCLink.CCLinkQueueList[fori].UserList[forj].UserEnterTime = eventJson.QueueList[fori].UserList[forj].UserEnterTime;
                    }
                }
                CCLink.CCLinkMeetingRoomList.length = 0;
                for (var fori = 0; fori < eventJson.MeetingRoomList.length; fori++) {
                    CCLink.CCLinkMeetingRoomList[fori] = new TCCLinkMeetingRoom();
                    CCLink.CCLinkMeetingRoomList[fori].Assigned = 0;
                    CCLink.CCLinkMeetingRoomList[fori].MeetingRoomCode = eventJson.MeetingRoomList[fori].MeetingRoomCode;
                    CCLink.CCLinkMeetingRoomList[fori].MeetingRoomPassWD = eventJson.MeetingRoomList[fori].MeetingRoomCode;
                    CCLink.CCLinkMeetingRoomList[fori].MeetingRoomName = eventJson.MeetingRoomList[fori].MeetingRoomName;
                    CCLink.CCLinkMeetingRoomList[fori].MeetingRoomMode = eventJson.MeetingRoomList[fori].MeetingRoomMode;
                    CCLink.CCLinkMeetingRoomList[fori].MemberList = new Array();
                    CCLink.CCLinkMeetingRoomList[fori].MemberList.length = 0;
                    for (var forj = 0; forj < eventJson.MeetingRoomList[fori].MemberList.length; forj++) {
                        CCLink.CCLinkMeetingRoomList[fori].MemberList[forj] = new TCCLinkMeetingRoomMember();
                        CCLink.CCLinkMeetingRoomList[fori].MemberList[forj].MemberID = eventJson.MeetingRoomList[fori].MemberList[forj].MemberID;
                        CCLink.CCLinkMeetingRoomList[fori].MemberList[forj].MemberCode = eventJson.MeetingRoomList[fori].MemberList[forj].MemberCode;
                        CCLink.CCLinkMeetingRoomList[fori].MemberList[forj].MemberRole = eventJson.MeetingRoomList[fori].MemberList[forj].MemberRole;
                        CCLink.CCLinkMeetingRoomList[fori].MemberList[forj].MemberType = eventJson.MeetingRoomList[fori].MemberList[forj].MemberType;
                        CCLink.CCLinkMeetingRoomList[fori].MemberList[forj].EnterTime = eventJson.MeetingRoomList[fori].MemberList[forj].EnterTime;
                        CCLink.CCLinkMeetingRoomList[fori].MemberList[forj].ChannelData = eventJson.MeetingRoomList[fori].MemberList[forj].ChannelData;
                    }
                }
                CCLink.CCLinkTrunkList.length = 0;
                for (var fori = 0; fori < eventJson.TrunkList.length; fori++) {
                    CCLink.CCLinkTrunkList[fori] = new TCCLinkTrunk();
                    CCLink.CCLinkTrunkList[fori].Assigned = 0;
                    CCLink.CCLinkTrunkList[fori].ChannelNo = eventJson.TrunkList[fori].ChannelNo;
                    CCLink.CCLinkTrunkList[fori].CallState = eventJson.TrunkList[fori].CallState;
                    CCLink.CCLinkTrunkList[fori].ChannelCode = eventJson.TrunkList[fori].ChannelCode;
                    CCLink.CCLinkTrunkList[fori].ChannelRole = eventJson.TrunkList[fori].ChannelRole;
                    CCLink.CCLinkTrunkList[fori].CallSessionID = eventJson.TrunkList[fori].CallSessionID;
                    CCLink.CCLinkTrunkList[fori].CallDirect = eventJson.TrunkList[fori].CallDirect;
                    CCLink.CCLinkTrunkList[fori].ANI = eventJson.TrunkList[fori].ANI;
                    CCLink.CCLinkTrunkList[fori].DNIS = eventJson.TrunkList[fori].DNIS;
                    CCLink.CCLinkTrunkList[fori].ChannelData = eventJson.TrunkList[fori].ChannelData;
                    CCLink.CCLinkTrunkList[fori].CallTime = eventJson.TrunkList[fori].CallTime;
                    CCLink.CCLinkTrunkList[fori].ConnectTime = eventJson.TrunkList[fori].ConnectTime;
                    CCLink.CCLinkTrunkList[fori].OtherCode = eventJson.TrunkList[fori].OtherCode;
                    CCLink.CCLinkTrunkList[fori].OtherRole = eventJson.TrunkList[fori].OtherRole;
                    CCLink.CCLinkTrunkList[fori].ChannelInfo = eventJson.TrunkList[fori].ChannelInfo;
                    CCLink.CCLinkTrunkList[fori].LogEnabled = eventJson.TrunkList[fori].LogEnabled;
                }
                if (CCLink.CCLinkState == 0) {
                    CCLink.CCLinkState = 1;
                    if (CCLink.LinkEvent_OnConnected) {
                        CCLink.LinkEvent_OnConnected.call(CCLink);
                    }
                }
                CCLink.CCLinkEventID = 0;
                CCLink.CCLinkRefreshTime = new Date();
                CCLinkHeartBeat();
            }
            if (eventJson.Action == "LinkEvent_OnDisconnected") {
                if ((eventJson.Error != null) && (eventJson.Error != "")) {
                    alert(eventJson.Error);
                }
                CCLink.CCLinkState = 0;
                CCLink.CCLinkInvokeFailTime = (new Date() - 3600000);
            }
            if (eventJson.Action == "ExtEvent_Assigned") {
                for (var fori = 0; fori < eventJson.ExtList.length; fori++) {
                    for (var forj = 0; forj < CCLink.CCLinkExtList.length; forj++) {
                        if (CCLink.CCLinkExtList[forj].ExtCode == eventJson.ExtList[fori].ExtCode) {
                            CCLink.CCLinkExtList[forj].ExtCode = eventJson.ExtList[fori].ExtCode;
                            CCLink.CCLinkExtList[forj].ExtPassWD = eventJson.ExtList[fori].ExtPassWD;
                            CCLink.CCLinkExtList[forj].ExtWorkNo = eventJson.ExtList[fori].ExtWorkNo;
                            CCLink.CCLinkExtList[forj].ExtName = eventJson.ExtList[fori].ExtName;
                            CCLink.CCLinkExtList[forj].ExtCaller = eventJson.ExtList[fori].ExtCaller;
                            CCLink.CCLinkExtList[forj].ExtType = eventJson.ExtList[fori].ExtType;
                            CCLink.CCLinkExtList[forj].CallState = eventJson.ExtList[fori].CallState;
                            CCLink.CCLinkExtList[forj].CallDirect = eventJson.ExtList[fori].CallDirect;
                            CCLink.CCLinkExtList[forj].ExtDoNotDisturbTime = eventJson.ExtList[fori].ExtDoNotDisturbTime;
                            CCLink.CCLinkExtList[forj].ExtDoNotDisturb = eventJson.ExtList[fori].ExtDoNotDisturb;
                            CCLink.CCLinkExtList[forj].LoginMode = eventJson.ExtList[fori].LoginMode;
                            CCLink.CCLinkExtList[forj].LastCallResult = eventJson.ExtList[fori].LastCallResult;
                            CCLink.CCLinkExtList[forj].ANI = eventJson.ExtList[fori].ANI;
                            CCLink.CCLinkExtList[forj].DNIS = eventJson.ExtList[fori].DNIS;
                            CCLink.CCLinkExtList[forj].ChannelData = eventJson.ExtList[fori].ChannelData;
                            CCLink.CCLinkExtList[forj].OtherANI = eventJson.ExtList[fori].OtherANI;
                            CCLink.CCLinkExtList[forj].OtherDNIS = eventJson.ExtList[fori].OtherDNIS;
                            CCLink.CCLinkExtList[forj].OtherChannelData = eventJson.ExtList[fori].OtherChannelData;
                            CCLink.CCLinkExtList[forj].CallSessionID = eventJson.ExtList[fori].CallSessionID;
                            CCLink.CCLinkExtList[forj].CallIndex = eventJson.ExtList[fori].CallIndex;
                            CCLink.CCLinkExtList[forj].CallTime = eventJson.ExtList[fori].CallTime;
                            CCLink.CCLinkExtList[forj].ConnectTime = eventJson.ExtList[fori].ConnectTime;
                            CCLink.CCLinkExtList[forj].RecordFile = eventJson.ExtList[fori].RecordFile;
                            CCLink.CCLinkExtList[forj].OtherCode = eventJson.ExtList[fori].OtherCode;
                            CCLink.CCLinkExtList[forj].OtherRole = eventJson.ExtList[fori].OtherRole;
                            CCLink.CCLinkExtList[forj].TransferCode = eventJson.ExtList[fori].TransferCode;
                            CCLink.CCLinkExtList[forj].TransferRole = eventJson.ExtList[fori].TransferRole;
                            CCLink.CCLinkExtList[forj].CallData = eventJson.ExtList[fori].CallData;
                            CCLink.CCLinkExtList[forj].QueueState = eventJson.ExtList[fori].QueueState;
                            if (CCLink.ExtEvent_OnAssigned) {
                                CCLink.ExtEvent_OnAssigned.call(CCLink, CCLink.CCLinkExtList[forj].ExtCode);
                            }
                            if (CCLink.ExtEvent_OnCallStateChange) {
                                CCLink.ExtEvent_OnCallStateChange.call(CCLink, CCLink.CCLinkExtList[forj].ExtCode, CCLink.CCLinkExtList[forj].CallState);
                            }
                            break;
                        }
                    }
                }
            }
            if (eventJson.Action == "ExtEvent_OnLogin") {
                var ExtIndex = CCLink.ExtIndexByCode(eventJson.ExtCode);
                if (ExtIndex >= 0) {
                    CCLink.CCLinkExtList[ExtIndex].LoginMode = eventJson.LoginMode;
                    if (CCLink.CCLinkExtList[ExtIndex].Assigned != 0) {
                        if (CCLink.ExtEvent_OnLogin) {
                            CCLink.ExtEvent_OnLogin.call(CCLink, eventJson.ExtCode);
                        }
                    }
                }
            }
            if (eventJson.Action == "ExtEvent_OnLogout") {
                var ExtIndex = CCLink.ExtIndexByCode(eventJson.ExtCode);
                if (ExtIndex >= 0) {
                    CCLink.CCLinkExtList[ExtIndex].LoginMode = eventJson.LoginMode;
                    if (CCLink.CCLinkExtList[ExtIndex].Assigned != 0) {
                        if (CCLink.ExtEvent_OnLogout) {
                            CCLink.ExtEvent_OnLogout.call(CCLink, eventJson.ExtCode);
                        }
                    }
                }
            }
            if (eventJson.Action == "ExtEvent_OnCallStateChange") {
                var ExtIndex = CCLink.ExtIndexByCode(eventJson.ExtCode);
                if (ExtIndex >= 0) {
                    CCLink.CCLinkExtList[ExtIndex].LoginMode = eventJson.LoginMode;
                    CCLink.CCLinkExtList[ExtIndex].CallState = eventJson.CallState;
                    CCLink.CCLinkExtList[ExtIndex].CallDirect = eventJson.CallDirect;
                    CCLink.CCLinkExtList[ExtIndex].ANI = eventJson.ANI;
                    CCLink.CCLinkExtList[ExtIndex].DNIS = eventJson.DNIS;
                    CCLink.CCLinkExtList[ExtIndex].OtherANI = eventJson.OtherANI;
                    CCLink.CCLinkExtList[ExtIndex].OtherDNIS = eventJson.OtherDNIS;
                    CCLink.CCLinkExtList[ExtIndex].OtherChannelData = eventJson.OtherChannelData;
                    CCLink.CCLinkExtList[ExtIndex].CallSessionID = eventJson.CallSessionID;
                    CCLink.CCLinkExtList[ExtIndex].CallIndex = eventJson.CallIndex;
                    CCLink.CCLinkExtList[ExtIndex].CallTime = eventJson.CallTime;
                    CCLink.CCLinkExtList[ExtIndex].ConnectTime = eventJson.ConnectTime;
                    CCLink.CCLinkExtList[ExtIndex].RecordFile = eventJson.RecordFile;
                    CCLink.CCLinkExtList[ExtIndex].OtherCode = eventJson.OtherCode;
                    CCLink.CCLinkExtList[ExtIndex].OtherRole = eventJson.OtherRole;
                    CCLink.CCLinkExtList[ExtIndex].TransferCode = eventJson.TransferCode;
                    CCLink.CCLinkExtList[ExtIndex].TransferRole = eventJson.TransferRole;
                    CCLink.CCLinkExtList[ExtIndex].CallData = eventJson.CallData;
                    if (CCLink.CCLinkExtList[ExtIndex].Assigned != 0) {
                        if (CCLink.ExtEvent_OnCallStateChange) {
                            CCLink.ExtEvent_OnCallStateChange.call(CCLink, eventJson.ExtCode, eventJson.CallState);
                        }
                    }
                }
            }
            if (eventJson.Action == "ExtEvent_OnCallIn") {
                var ExtIndex = CCLink.ExtIndexByCode(eventJson.ExtCode);
                if (ExtIndex >= 0) {
                    CCLink.CCLinkExtList[ExtIndex].OtherCode = eventJson.OtherCode;
                    CCLink.CCLinkExtList[ExtIndex].OtherRole = eventJson.OtherRole;
                    CCLink.CCLinkExtList[ExtIndex].TransferCode = eventJson.TransferCode;
                    CCLink.CCLinkExtList[ExtIndex].TransferRole = eventJson.TransferRole;
                    CCLink.CCLinkExtList[ExtIndex].CallData = eventJson.CallData;
                    if (CCLink.CCLinkExtList[ExtIndex].Assigned != 0) {
                        if (CCLink.ExtEvent_OnCallIn) {
                            CCLink.ExtEvent_OnCallIn.call(CCLink, eventJson.ExtCode, eventJson.OtherCode, eventJson.OtherRole, eventJson.TransferCode, eventJson.TransferRole, eventJson.CallData);
                        }
                    }
                }
            }
            if (eventJson.Action == "ExtEvent_OnConnected") {
                var ExtIndex = CCLink.ExtIndexByCode(eventJson.ExtCode);
                if (ExtIndex >= 0) {
                    CCLink.CCLinkExtList[ExtIndex].OtherCode = eventJson.OtherCode;
                    CCLink.CCLinkExtList[ExtIndex].OtherRole = eventJson.OtherRole;
                    CCLink.CCLinkExtList[ExtIndex].TransferCode = eventJson.TransferCode;
                    CCLink.CCLinkExtList[ExtIndex].TransferRole = eventJson.TransferRole;
                    CCLink.CCLinkExtList[ExtIndex].CallData = eventJson.CallData;
                    if (CCLink.CCLinkExtList[ExtIndex].Assigned != 0) {
                        if (CCLink.ExtEvent_OnConnected) {
                            CCLink.ExtEvent_OnConnected.call(CCLink, eventJson.ExtCode, eventJson.OtherCode, eventJson.OtherRole, eventJson.TransferCode, eventJson.TransferRole, eventJson.CallData);
                        }
                    }
                }
            }
            if (eventJson.Action == "ExtEvent_OnConsultation") {
                var ExtIndex = CCLink.ExtIndexByCode(eventJson.ExtCode);
                if (ExtIndex >= 0) {
                    CCLink.CCLinkExtList[ExtIndex].OtherCode = eventJson.OtherCode;
                    CCLink.CCLinkExtList[ExtIndex].OtherRole = eventJson.OtherRole;
                    CCLink.CCLinkExtList[ExtIndex].TransferCode = eventJson.TransferCode;
                    CCLink.CCLinkExtList[ExtIndex].TransferRole = eventJson.TransferRole;
                    CCLink.CCLinkExtList[ExtIndex].CallData = eventJson.CallData;
                    if (CCLink.CCLinkExtList[ExtIndex].Assigned != 0) {
                        if (CCLink.ExtEvent_OnConsultation) {
                            CCLink.ExtEvent_OnConsultation.call(CCLink, eventJson.ExtCode, eventJson.OtherCode, eventJson.OtherRole, eventJson.TransferCode, eventJson.TransferRole, eventJson.CallData);
                        }
                    }
                }
            }
            if (eventJson.Action == "ExtEvent_OnDisConnected") {
                var ExtIndex = CCLink.ExtIndexByCode(eventJson.ExtCode);
                if (ExtIndex >= 0) {
                    if (CCLink.CCLinkExtList[ExtIndex].Assigned != 0) {
                        if (CCLink.ExtEvent_OnDisconnected) {
                            CCLink.ExtEvent_OnDisconnected.call(CCLink, eventJson.ExtCode);
                        }
                    }
                }
            }
            if (eventJson.Action == "ExtEvent_OnCallFinished") {
                var ExtIndex = CCLink.ExtIndexByCode(eventJson.ExtCode);
                if (ExtIndex >= 0) {
                    CCLink.CCLinkExtList[ExtIndex].LastCallResult = eventJson.CallResult;
                    if (CCLink.CCLinkExtList[ExtIndex].Assigned != 0) {
                        if (CCLink.ExtEvent_OnCallFinished) {
                            CCLink.ExtEvent_OnCallFinished.call(CCLink, eventJson.ExtCode, eventJson.CallResult);
                        }
                    }
                }
            }
            if (eventJson.Action == "ExtEvent_OnReceiveMessage") {
                var ExtIndex = CCLink.ExtIndexByCode(eventJson.ExtCode);
                if (ExtIndex >= 0) {
                    if (CCLink.CCLinkExtList[ExtIndex].Assigned != 0) {
                        if (CCLink.ExtEvent_OnReceiveMessage) {
                            CCLink.ExtEvent_OnReceiveMessage.call(CCLink, eventJson.ExtCode, eventJson.OtherCode, eventJson.Message);
                        }
                    }
                }
            }
            if (eventJson.Action == "ExtEvent_OnDoNotDisturbChange") {
                var ExtIndex = CCLink.ExtIndexByCode(eventJson.ExtCode);
                if (ExtIndex >= 0) {
                    CCLink.CCLinkExtList[ExtIndex].ExtDoNotDisturb = eventJson.DoNotDisturb;
                    CCLink.CCLinkExtList[ExtIndex].ExtDoNotDisturbTime = eventJson.DoNotDisturbTime;
                    if (CCLink.CCLinkExtList[ExtIndex].Assigned != 0) {
                        if (CCLink.ExtEvent_OnDoNotDisturbChange) {
                            CCLink.ExtEvent_OnDoNotDisturbChange.call(CCLink, eventJson.ExtCode, eventJson.DoNotDisturb);
                        }
                    }
                }
            }
            if (eventJson.Action == "ExtEvent_OnExtNameChange") {
                var ExtIndex = CCLink.ExtIndexByCode(eventJson.ExtCode);
                if (ExtIndex >= 0) {
                    CCLink.CCLinkExtList[ExtIndex].ExtName = eventJson.ExtName;
                }
            }
            if (eventJson.Action == "ExtEvent_OnExtWorkNoChange") {
                var ExtIndex = CCLink.ExtIndexByCode(eventJson.ExtCode);
                if (ExtIndex >= 0) {
                    CCLink.CCLinkExtList[ExtIndex].ExtWorkNo = eventJson.ExtWorkNo;
                }
            }
            if (eventJson.Action == "ExtEvent_OnCallDataChange") {
                var ExtIndex = CCLink.ExtIndexByCode(eventJson.ExtCode);
                if (ExtIndex >= 0) {
                    CCLink.CCLinkExtList[ExtIndex].CallData = eventJson.CallData;
                }
            }
            if (eventJson.Action == "ExtEvent_OnPlayVoiceEnd") {
                var ExtIndex = CCLink.ExtIndexByCode(eventJson.ExtCode);
                if (ExtIndex >= 0) {
                    if (CCLink.CCLinkExtList[ExtIndex].Assigned != 0) {
                        if (CCLink.ExtEvent_OnPlayVoiceEnd) {
                            CCLink.ExtEvent_OnPlayVoiceEnd.call(CCLink, eventJson.ExtCode);
                        }
                    }
                }
            }
            if (eventJson.Action == "ExtEvent_OnQueueStateChange") {
                var ExtIndex = CCLink.ExtIndexByCode(eventJson.ExtCode);
                if (ExtIndex >= 0) {
                    CCLink.CCLinkExtList[ExtIndex].QueueState = eventJson.QueueState;
                    if (CCLink.CCLinkExtList[ExtIndex].Assigned != 0) {
                        if (CCLink.ExtEvent_OnQueueStateChange) {
                            CCLink.ExtEvent_OnQueueStateChange.call(CCLink, eventJson.ExtCode, eventJson.QueueState);
                        }
                    }
                }
            }
            if (eventJson.Action == "QueueEvent_Assigned") {
                for (var fori = 0; fori < eventJson.QueueList.length; fori++) {
                    for (var forj = 0; forj < CCLink.CCLinkQueueList.length; forj++) {
                        if (CCLink.CCLinkQueueList[forj].QueueCode == eventJson.QueueList[fori].QueueCode) {
                            CCLink.CCLinkQueueList[forj].QueueName = eventJson.QueueList[fori].QueueName;
                            CCLink.CCLinkQueueList[forj].ExtList = new Array();
                            CCLink.CCLinkQueueList[forj].ExtList.length = 0;
                            for (var fork = 0; fork < eventJson.QueueList[fori].ExtList.length; fork++) {
                                CCLink.CCLinkQueueList[forj].ExtList[fork] = new TCCLinkQueueExt();
                                CCLink.CCLinkQueueList[forj].ExtList[fork].ExtCode = eventJson.QueueList[fori].ExtList[fork].ExtCode;
                                CCLink.CCLinkQueueList[forj].ExtList[fork].Pri = eventJson.QueueList[fori].ExtList[fork].Pri;
                            }
                            CCLink.CCLinkQueueList[forj].UserList = new Array();
                            CCLink.CCLinkQueueList[forj].UserList.length = 0;
                            for (var fork = 0; fork < eventJson.QueueList[fori].UserList.length; fork++) {
                                CCLink.CCLinkQueueList[forj].UserList[fork] = new TCCLinkQueueUser();
                                CCLink.CCLinkQueueList[forj].UserList[fork].UserID = eventJson.QueueList[fori].UserList[fork].UserID;
                                CCLink.CCLinkQueueList[forj].UserList[fork].UserCode = eventJson.QueueList[fori].UserList[fork].UserCode;
                                CCLink.CCLinkQueueList[forj].UserList[fork].UserRole = eventJson.QueueList[fori].UserList[fork].UserRole;
                                CCLink.CCLinkQueueList[forj].UserList[fork].UserEnterTime = eventJson.QueueList[fori].UserList[fork].UserEnterTime;
                            }
                            if (CCLink.QueueEvent_OnAssigned) {
                                CCLink.QueueEvent_OnAssigned.call(CCLink, eventJson.QueueList[fori].QueueCode);
                            }
                        }
                    }
                }
            }
            if (eventJson.Action == "QueueEvent_OnAddExt") {
                var QueueIndex = CCLink.QueueIndexByCode(eventJson.QueueCode);
                if (QueueIndex >= 0) {
                    var ExtIndex = CCLink.CCLinkQueueList[QueueIndex].ExtList.length;
                    CCLink.CCLinkQueueList[QueueIndex].ExtList[ExtIndex] = new TCCLinkQueueExt();
                    CCLink.CCLinkQueueList[QueueIndex].ExtList[ExtIndex].ExtCode = eventJson.ExtCode;
                    CCLink.CCLinkQueueList[QueueIndex].ExtList[ExtIndex].Pri = eventJson.Pri;
                    if (CCLink.CCLinkQueueList[QueueIndex].Assigned != 0) {
                        if (CCLink.QueueEvent_OnAddExt) {
                            CCLink.QueueEvent_OnAddExt.call(CCLink, eventJson.QueueCode, eventJson.ExtCode, eventJson.Pri);
                        }
                    }
                }
            }
            if (eventJson.Action == "QueueEvent_OnSubExt") {
                var QueueIndex = CCLink.QueueIndexByCode(eventJson.QueueCode);
                if (QueueIndex >= 0) {
                    var ExtIndex = -1;
                    for (var fori = 0; fori < CCLink.CCLinkQueueList[QueueIndex].ExtList.length; fori++) {
                        if (CCLink.CCLinkQueueList[QueueIndex].ExtList[fori].ExtCode == eventJson.ExtCode) {
                            ExtIndex = fori;
                            break;
                        }
                    }
                    if (ExtIndex != -1) {
                        for (var fori = ExtIndex; fori < CCLink.CCLinkQueueList[QueueIndex].ExtList.length - 1; fori++) {
                            CCLink.CCLinkQueueList[QueueIndex].ExtList[fori].ExtCode = CCLink.CCLinkQueueList[QueueIndex].ExtList[fori + 1].ExtCode;
                            CCLink.CCLinkQueueList[QueueIndex].ExtList[fori].Pri = CCLink.CCLinkQueueList[QueueIndex].ExtList[fori + 1].Pri;
                        }
                        CCLink.CCLinkQueueList[QueueIndex].ExtList.length = CCLink.CCLinkQueueList[QueueIndex].ExtList.length - 1;
                    }
                    if (CCLink.CCLinkQueueList[QueueIndex].Assigned != 0) {
                        if (CCLink.QueueEvent_OnSubExt) {
                            CCLink.QueueEvent_OnSubExt.call(CCLink, eventJson.QueueCode, eventJson.ExtCode);
                        }
                    }
                }
            }
            if (eventJson.Action == "QueueEvent_OnAddUser") {
                var QueueIndex = CCLink.QueueIndexByCode(eventJson.QueueCode);
                if (QueueIndex >= 0) {
                    var UserIndex = CCLink.CCLinkQueueList[QueueIndex].UserList.length;
                    CCLink.CCLinkQueueList[QueueIndex].UserList[UserIndex] = new TCCLinkQueueUser();
                    CCLink.CCLinkQueueList[QueueIndex].UserList[UserIndex].UserID = eventJson.UserID;
                    CCLink.CCLinkQueueList[QueueIndex].UserList[UserIndex].UserCode = eventJson.UserCode;
                    CCLink.CCLinkQueueList[QueueIndex].UserList[UserIndex].UserRole = eventJson.UserRole;
                    CCLink.CCLinkQueueList[QueueIndex].UserList[UserIndex].UserEnterTime = eventJson.UserEnterTime;
                    if (CCLink.CCLinkQueueList[QueueIndex].Assigned != 0) {
                        if (CCLink.QueueEvent_OnAddUser) {
                            CCLink.QueueEvent_OnAddUser.call(CCLink, eventJson.QueueCode, eventJson.UserID, eventJson.UserCode, eventJson.UserRole);
                        }
                    }
                }
            }
            if (eventJson.Action == "QueueEvent_OnSubUser") {
                var QueueIndex = CCLink.QueueIndexByCode(eventJson.QueueCode);
                if (QueueIndex >= 0) {
                    var UserIndex = -1;
                    for (var fori = 0; fori < CCLink.CCLinkQueueList[QueueIndex].UserList.length; fori++) {
                        if (CCLink.CCLinkQueueList[QueueIndex].UserList[fori].UserID == eventJson.UserID) {
                            UserIndex = fori;
                            break;
                        }
                    }
                    if (UserIndex != -1) {
                        for (var fori = UserIndex; fori < CCLink.CCLinkQueueList[QueueIndex].UserList.length - 1; fori++) {
                            CCLink.CCLinkQueueList[QueueIndex].UserList[fori].UserID = CCLink.CCLinkQueueList[QueueIndex].UserList[fori + 1].UserID;
                            CCLink.CCLinkQueueList[QueueIndex].UserList[fori].UserCode = CCLink.CCLinkQueueList[QueueIndex].UserList[fori + 1].UserCode;
                            CCLink.CCLinkQueueList[QueueIndex].UserList[fori].UserRole = CCLink.CCLinkQueueList[QueueIndex].UserList[fori + 1].UserRole;
                            CCLink.CCLinkQueueList[QueueIndex].UserList[fori].UserEnterTime = CCLink.CCLinkQueueList[QueueIndex].UserList[fori + 1].UserEnterTime;
                        }
                        CCLink.CCLinkQueueList[QueueIndex].UserList.length = CCLink.CCLinkQueueList[QueueIndex].UserList.length - 1;
                    }
                    if (CCLink.CCLinkQueueList[QueueIndex].Assigned != 0) {
                        if (CCLink.QueueEvent_OnSubUser) {
                            CCLink.QueueEvent_OnSubUser.call(CCLink, eventJson.QueueCode, eventJson.UserID);
                        }
                    }
                }
            }
            if (eventJson.Action == "MeetingRoomEvent_Assigned") {
                for (var fori = 0; fori < eventJson.MeetingRoomList.length; fori++) {
                    for (var forj = 0; forj < CCLink.CCLinkMeetingRoomList.length; forj++) {
                        if (CCLink.CCLinkMeetingRoomList[forj].MeetingRoomCode == eventJson.MeetingRoomList[fori].MeetingRoomCode) {
                            CCLink.CCLinkMeetingRoomList[forj].MeetingRoomName = eventJson.MeetingRoomList[fori].MeetingRoomName;
                            CCLink.CCLinkMeetingRoomList[forj].MeetingRoomMode = eventJson.MeetingRoomList[fori].MeetingRoomMode;
                            CCLink.CCLinkMeetingRoomList[forj].MemberList = new Array();
                            CCLink.CCLinkMeetingRoomList[forj].MemberList.length = 0;
                            for (var fork = 0; fork < eventJson.MeetingRoomList[fori].MemberList.length; fork++) {
                                CCLink.CCLinkMeetingRoomList[forj].MemberList[fork] = new TCCLinkMeetingRoomMember();
                                CCLink.CCLinkMeetingRoomList[forj].MemberList[fork].MemberID = eventJson.MeetingRoomList[fori].MemberList[fork].MemberID;
                                CCLink.CCLinkMeetingRoomList[forj].MemberList[fork].MemberCode = eventJson.MeetingRoomList[fori].MemberList[fork].MemberCode;
                                CCLink.CCLinkMeetingRoomList[forj].MemberList[fork].MemberRole = eventJson.MeetingRoomList[fori].MemberList[fork].MemberRole;
                                CCLink.CCLinkMeetingRoomList[forj].MemberList[fork].MemberType = eventJson.MeetingRoomList[fori].MemberList[fork].MemberType;
                                CCLink.CCLinkMeetingRoomList[forj].MemberList[fork].EnterTime = eventJson.MeetingRoomList[fori].MemberList[fork].EnterTime;
                                CCLink.CCLinkMeetingRoomList[forj].MemberList[fork].ChannelData = eventJson.MeetingRoomList[fori].MemberList[fork].ChannelData;
                            }
                        }
                    }
                }
            }
            if (eventJson.Action == "MeetingRoomEvent_OnAddMember") {
                var MeetingRoomIndex = CCLink.MeetingRoomIndexByCode(eventJson.MeetingRoomCode);
                if (MeetingRoomIndex >= 0) {
                    var MemberIndex = CCLink.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList.length;
                    CCLink.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList[MemberIndex] = new TCCLinkMeetingRoomMember();
                    CCLink.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList[MemberIndex].MemberID = eventJson.MemberID;
                    CCLink.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList[MemberIndex].MemberCode = eventJson.MemberCode;
                    CCLink.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList[MemberIndex].MemberRole = eventJson.MemberRole;
                    CCLink.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList[MemberIndex].MemberType = eventJson.MemberType;
                    CCLink.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList[MemberIndex].EnterTime = eventJson.EnterTime;
                    if (CCLink.CCLinkMeetingRoomList[MeetingRoomIndex].Assigned != 0) {
                        if (CCLink.MeetingRoomEvent_OnAddMember) {
                            CCLink.MeetingRoomEvent_OnAddMember.call(CCLink, eventJson.MeetingRoomCode, eventJson.MemberID, eventJson.MemberCode, eventJson.MemberType);
                        }
                    }
                }
            }
            if (eventJson.Action == "MeetingRoomEvent_OnDeleteMember") {
                var MeetingRoomIndex = CCLink.MeetingRoomIndexByCode(eventJson.MeetingRoomCode);
                if (MeetingRoomIndex >= 0) {
                    var MemberIndex = -1;
                    for (var fori = 0; fori < CCLink.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList.length; fori++) {
                        if (CCLink.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList[fori].MemberID == eventJson.MemberID) {
                            MemberIndex = fori;
                            break;
                        }
                    }
                    if (MemberIndex != -1) {
                        for (var fori = MemberIndex; fori < CCLink.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList.length - 1; fori++) {
                            CCLink.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList[fori].MemberID = CCLink.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList[fori + 1].MemberID;
                            CCLink.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList[fori].MemberCode = CCLink.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList[fori + 1].MemberCode;
                            CCLink.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList[fori].MemberRole = CCLink.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList[fori + 1].MemberRole;
                            CCLink.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList[fori].MemberType = CCLink.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList[fori + 1].MemberType;
                            CCLink.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList[fori].EnterTime = CCLink.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList[fori + 1].EnterTime;
                        }
                        CCLink.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList.length = CCLink.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList.length - 1;
                    }
                    if (CCLink.CCLinkMeetingRoomList[MeetingRoomIndex].Assigned != 0) {
                        if (CCLink.MeetingRoomEvent_OnDeleteMember) {
                            CCLink.MeetingRoomEvent_OnDeleteMember.call(CCLink, eventJson.MeetingRoomCode, eventJson.MemberID);
                        }
                    }
                }
            }
            if (eventJson.Action == "MeetingRoomEvent_OnMemberTypeChange") {
                var MeetingRoomIndex = CCLink.MeetingRoomIndexByCode(eventJson.MeetingRoomCode);
                if (MeetingRoomIndex >= 0) {
                    var MemberIndex = -1;
                    for (var fori = 0; fori < CCLink.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList.length; fori++) {
                        if (CCLink.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList[MemberIndex].MemberID == eventJson.MemberID) {
                            MemberIndex = fori;
                            break;
                        }
                    }
                    if (MemberIndex != -1) {
                        CCLink.CCLinkMeetingRoomList[MeetingRoomIndex].MemberList[MemberIndex].MemberType = eventJson.MemberType;
                    }
                    if (CCLink.CCLinkMeetingRoomList[MeetingRoomIndex].Assigned != 0) {
                        if (CCLink.MeetingRoomEvent_OnMemberTypeChange) {
                            CCLink.MeetingRoomEvent_OnMemberTypeChange.call(CCLink, eventJson.MeetingRoomCode, eventJson.MemberID, eventJson.MemberType);
                        }
                    }
                }
            }
            if (eventJson.Action == "TrunkEvent_Assigned") {
                for (var fori = 0; fori < eventJson.TrunkList.length; fori++) {
                    for (var forj = 0; forj < CCLink.CCLinkTrunkList.length; forj++) {
                        if (CCLink.CCLinkTrunkList[forj].ChannelNo == eventJson.TrunkList[fori].ChannelNo) {
                            CCLink.CCLinkTrunkList[forj].CallState = eventJson.TrunkList[fori].CallState;
                            CCLink.CCLinkTrunkList[forj].ChannelCode = eventJson.TrunkList[fori].ChannelCode;
                            CCLink.CCLinkTrunkList[forj].ChannelRole = eventJson.TrunkList[fori].ChannelRole;
                            CCLink.CCLinkTrunkList[forj].CallSessionID = eventJson.TrunkList[fori].CallSessionID;
                            CCLink.CCLinkTrunkList[forj].CallDirect = eventJson.TrunkList[fori].CallDirect;
                            CCLink.CCLinkTrunkList[forj].ANI = eventJson.TrunkList[fori].ANI;
                            CCLink.CCLinkTrunkList[forj].DNIS = eventJson.TrunkList[fori].DNIS;
                            CCLink.CCLinkTrunkList[forj].ChannelData = eventJson.TrunkList[fori].ChannelData;
                            CCLink.CCLinkTrunkList[forj].CallTime = eventJson.TrunkList[fori].CallTime;
                            CCLink.CCLinkTrunkList[forj].ConnectTime = eventJson.TrunkList[fori].ConnectTime;
                            CCLink.CCLinkTrunkList[forj].OtherCode = eventJson.TrunkList[fori].OtherCode;
                            CCLink.CCLinkTrunkList[forj].OtherRole = eventJson.TrunkList[fori].OtherRole;
                            CCLink.CCLinkTrunkList[forj].ChannelInfo = eventJson.TrunkList[fori].ChannelInfo;
                            CCLink.CCLinkTrunkList[forj].LogEnabled = eventJson.TrunkList[fori].LogEnabled;
                        }
                    }
                }
            }
            if (eventJson.Action == "TrunkEvent_OnChannelInfoChange") {
                var TrunkIndex = CCLink.TrunkIndexByChannelNo(eventJson.ChannelNo);
                if (TrunkIndex >= 0) {
                    CCLink.CCLinkTrunkList[TrunkIndex].ChannelInfo = eventJson.ChannelInfo;
                    if (CCLink.CCLinkTrunkList[TrunkIndex].Assigned != 0) {
                        if (CCLink.TrunkEvent_OnChannelInfoChange) {
                            CCLink.TrunkEvent_OnChannelInfoChange.call(CCLink, eventJson.ChannelNo, eventJson.ChannelInfo);
                        }
                    }
                }
            }
            if (eventJson.Action == "TrunkEvent_OnCallStateChange") {
                var TrunkIndex = CCLink.TrunkIndexByChannelNo(eventJson.ChannelNo);
                if (TrunkIndex >= 0) {
                    CCLink.CCLinkTrunkList[TrunkIndex].CallState = eventJson.CallState;
                    CCLink.CCLinkTrunkList[TrunkIndex].ChannelCode = eventJson.ChannelCode;
                    CCLink.CCLinkTrunkList[TrunkIndex].CallDirect = eventJson.CallDirect;
                    CCLink.CCLinkTrunkList[TrunkIndex].ChannelRole = eventJson.ChannelRole;
                    CCLink.CCLinkTrunkList[TrunkIndex].CallSessionID = eventJson.CallSessionID;
                    CCLink.CCLinkTrunkList[TrunkIndex].OtherCode = eventJson.OtherCode;
                    CCLink.CCLinkTrunkList[TrunkIndex].OtherRole = eventJson.OtherRole;
                    CCLink.CCLinkTrunkList[TrunkIndex].ANI = eventJson.ANI;
                    CCLink.CCLinkTrunkList[TrunkIndex].DNIS = eventJson.DNIS;
                    CCLink.CCLinkTrunkList[TrunkIndex].CallTime = eventJson.CallTime;
                    CCLink.CCLinkTrunkList[TrunkIndex].ConnectTime = eventJson.ConnectTime;
                    if (CCLink.CCLinkTrunkList[TrunkIndex].Assigned != 0) {
                        if (CCLink.TrunkEvent_OnCallStateChange) {
                            CCLink.TrunkEvent_OnCallStateChange.call(CCLink, eventJson.ChannelNo, eventJson.CallState);
                        }
                    }
                }
            }
            if (eventJson.Action == "SIPSoftPhoneEvent_OnRegister") {
                if (CCLink.SIPSoftPhoneEvent_OnRegister) {
                    CCLink.SIPSoftPhoneEvent_OnRegister.call(CCLink, eventJson.RegisterResult);
                }
            }
            if (eventJson.Action == "SIPSoftPhoneEvent_OnCallIn") {
                if (CCLink.SIPSoftPhoneEvent_OnCallIn) {
                    CCLink.SIPSoftPhoneEvent_OnCallIn.call(CCLink, eventJson.OtherCode);
                }
            }
            CCLink.CCLinkEventID = eventJson.EventID;
        }
    }
    CCLink.CCLinkInvokeFailTime = new Date();
}
