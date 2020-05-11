var Player = {
 'VodName': _$[0],
 'LisName': _$[1],
 'LisUrl': _$[2],
 'Id': _$[3],
 'Sid': _$[4],
 'Pid': _$[5],
 'UrlName': _$[6],
 'PlayerName': _$[7],
 'ServerUrl': _$[8],
 'Url': _$[9],
 'LastPid': _$[10],
 'LastWebPage': _$[11],
 'NextPid': _$[12],
 'NextUrl': _$[13],
 'NextUrlName': _$[14],
 'NextWebPage': _$[15],
 'ParentUrl': document.URL,
 'UrlJson': eval(_$[16] + ff_urls + _$[17]),
 'Root': ff_root,
 'Buffer': ff_buffer,
 'Pase': ff_buffer,
 'Width': ff_width,
 'Height': ff_height,
 'Second': ff_second,
 'Show': function() {
  if (ff_showlist == 0x1) {
   var list_show = _$[18]
  } else {
   var list_show = _$[19]
  };
  if (this.NextWebPage) {
   var NextWebPage = this.NextWebPage
  } else {
   var NextWebPage = this.ParentUrl
  };
  $$(_$[20]).innerHTML = _$[21] + this.LastWebPage + _$[22] + NextWebPage + _$[23];
  $$(_$[24]).innerHTML = _$[25] + this.ListUrl + _$[26] + this.ListName + _$[27] + this.VodName + _$[28] + this.UrlName + _$[29];
  $$(_$[30]).innerHTML = _$[31];
  $$(_$[32]).innerHTML = _$[33] + this.Buffer + _$[34] + this.Height + _$[35] + $Showhtml();
  $$(_$[36]).style.height = this.Height + _$[37];
  $$(_$[38]).innerHTML = _$[39] + list_show + _$[40] + this.Height + _$[41] + this.CreateList() + _$[42];
  document.write(_$[43] + _$[44] + _$[45])
 },
 'BufferHide': function() {
  $$(_$[46]).style.display = _$[47]
 },
 'CreateList': function() {
  var html = _$[48];
  for (var i = 0x0; i < this.UrlJson.Data.length; i++) {
   if (this.Sid == i) {
    ul_display = _$[49];
    h2class = _$[50]
   } else {
    ul_display = _$[51];
    h2class = _$[52]
   };
   var sid_on;
   var sub_on;
   var html_sub;
   html_sub = _$[53] + ul_display + _$[54] + i + _$[55];
   for (var j = 0x0; j < this.UrlJson.Data[i].playurls.length; j++) {
    var href = this.UrlJson.Data[i].playurls[j][0x2];
    if (this.Sid == i && this.Pid == (j + 0x1)) {
     var li_on = _$[56]
    } else {
     li_on = _$[57]
    };
    html_sub += _$[58] + href + _$[59] + this.UrlJson.Data[i].playurls[j][0x1] + _$[60] + li_on + _$[61] + this.UrlJson.Data[i].playurls[j][0x0] + _$[62]
   };
   html_sub += _$[63];
   html += _$[64] + i + _$[65] + h2class + _$[66];
   html += _$[67] + i + _$[68] + (this.UrlJson.Data.length - 0x1) + _$[69] + eval(_$[70] + this.UrlJson.Data[i].playname) + _$[71];
   html += html_sub;
   html += _$[72]
  };
  return html
 },
 'ShowList': function() {
  if ($$(_$[73]).style.display == _$[74]) {
   $$(_$[75]).style.display = _$[76]
  } else {
   $$(_$[77]).style.display = _$[78]
  }
 },
 'Tabs': function(no, n) {
  var subdisply = $$(_$[79] + no).style.display;
  for (var i = 0x0; i <= n; i++) {
   $$(_$[80] + i).className = _$[81];
   $$(_$[82] + i).style.display = _$[83]
  };
  $$(_$[84] + no).className = _$[85];
  if (subdisply == _$[86]) {
   $$(_$[87] + no).style.display = _$[88]
  } else {
   $$(_$[89] + no).style.display = _$[90]
  }
 },
 'Install': function() {
  var downurl = eval(_$[91] + this.PlayerName);
  $$(_$[92]).innerHTML = _$[93] + this.PlayerName + _$[94] + downurl + _$[95] + this.Height + _$[96];
  $$(_$[97]).style.display = _$[98]
 },
 'Html': function() {
  document.write(_$[99])
 },
 'Play': function() {
  this.Html();
  this.VodName = this.UrlJson.Vod[0x0];
  this.ListName = this.UrlJson.Vod[0x1];
  this.ListUrl = this.UrlJson.Vod[0x2];
  var URL = this.ParentUrl.match(/\d+.*/g)[0x0].match(/\d+/g);
  this.Id = URL[(URL.length - 0x3)] * 0x1;
  this.Sid = URL[(URL.length - 0x2)] * 0x1;
  this.Pid = URL[(URL.length - 0x1)] * 0x1;
  this.Pid = Math.min(this.Pid, this.UrlJson.Data[this.Sid].playurls.length);
  this.PlayerName = this.UrlJson.Data[this.Sid].playname;
  if (this.UrlJson.Data[this.Sid].servername) {
   this.ServerUrl = eval(_$[100] + this.UrlJson.Data[this.Sid].servername)
  };
  this.Url = this.ServerUrl + this.UrlJson.Data[this.Sid].playurls[(this.Pid - 0x1)][0x1];
  this.UrlName = this.UrlJson.Data[this.Sid].playurls[(this.Pid - 0x1)][0x0];
  this.LastPid = Math.max(Math.abs(this.Pid - 0x1), 0x1);
  this.LastWebPage = this.UrlJson.Data[this.Sid].playurls[this.LastPid - 0x1][0x2];
  this.NextPid = Math.min(this.Pid + 0x1, this.UrlJson.Data[this.Sid].playurls.length);
  this.NextUrl = this.ServerUrl + this.UrlJson.Data[this.Sid].playurls[this.NextPid - 0x1][0x1];
  this.NextUrlName = this.UrlJson.Data[this.Sid].playurls[this.NextPid - 0x1][0x0];
  if (this.Url == this.NextUrl) {
   this.NextWebPage = _$[101]
  } else {
   this.NextWebPage = this.UrlJson.Data[this.Sid].playurls[this.NextPid - 0x1][0x2]
  };
  document.write(_$[102] + _$[103] + this.Root + _$[104] + this.PlayerName + _$[105] + _$[106])
 }
}