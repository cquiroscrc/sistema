
//Version 1.12

//--- std.js

var W=window, D=document, F, E, EvSrc, Op, Gec, Ns, Saf, Nav, B;
//------------------------------------------------------------------------

function getbyid(id){return D.getElementById(id);}

function Contains(o, Ch){
while(Ch && Ch!==o && Ch!==D) Ch=Ch.parentNode;
return (Ch===o);
}

function EventInit(e){
E=(!Gec ? event : e); EvSrc=E[Gec ? 'target' : 'srcElement'];
if (Ns && EvSrc.nodeType==3) EvSrc=EvSrc.parentNode;
}

function LeftBtn(){
//Call EventInit() first; Op: since 9. Could also use E.buttons but Chrome misses it!

if (W.LBNo==null){
	if (Gec || Op) LBNo=0; else LBNo=1; //W.event; 0 for MouseEvent: (IEVer>=9 ? 0 : 1);
	}
return (E.button==LBNo);
}

function AbsPos(O, Parent){
var X=0, Y=0, Next;

Next=O; if (Parent==null) Parent=D;
while (Next!=null && Next!==Parent){
	Y+=Next.offsetTop; X+=Next.offsetLeft; Next=Next.offsetParent;
	}
return [X, Y];
}

function InitUA(){

if (Nav) return;
IEVer=0; FFVer=0; Nav=W.navigator.userAgent; 
Gec=(Nav.indexOf('Gecko')!=-1); Op=(Nav.indexOf('Opera')!=-1);
Saf=(Nav.indexOf(' AppleWebKit/')!=-1 && Nav.indexOf(' Safari/')!=-1);
Chrome=(Saf && Nav.indexOf(' Chrome/')!=-1); Quirks=(D.compatMode=='BackCompat');
iOs=(Saf && (Nav.indexOf('(iPhone;')!=-1 || Nav.indexOf('(iPad;')!=-1));
bAndr=(Saf && (Nav.indexOf(' Android ')!=-1));

if (!Gec && !Op && !Saf){
	if (D.documentMode){
		IEVer=D.documentMode; if (IEVer==5) IEVer=6;
		}
	else{
		if (Nav.match(/ MSIE (\d+)/)) IEVer=RegExp.$1;
		}
	if (Nav.indexOf('IEMobile')!=-1) IEMob=true;
	}
else if (Gec && Nav.match(/Firefox\/(\d+)/)){
	FFVer=RegExp.$1; if (FFVer>=4) FFVer=4;
	}
}

InitUA();

function GetPosRoot(){
return (D.getElementsByTagName((D.compatMode=='BackCompat' && !Saf) || Op ? 'BODY' : 'HTML')[0]);
}

//--- calendar.js

var oCal, nbsp=String.fromCharCode(160), Cal_MonFst=true;

function DrawCalFld(Name, Val, MinYear, MaxYear, bCell){
var Id=Name+'_Cal', DT, Span='', Events=['Over', 'Out', 'Down', 'Up'],
	i, Text='', NoVal, Cal;

if (!W.Cal_Delim) Cal_Delim='/';
if (W.Cal_MD==null) Cal_MD=true; 
if (!oCal) oCal=new Cal_Create();

NoVal=!Val; Val=Cal_InitVal(Val);	DT=Val.split(Cal_Delim); if (NoVal) Val='';

for(i=0; i<Events.length; i++) Span+=' OnMouse'+Events[i]+'="return Cal_FldMouse(event)"';
for(i=0; i<3; i++){ //8204 prevents FF kerning of '7.'
	Text+=(i>0 ? (FFVer ? String.fromCharCode(8204) : '')+Cal_Delim : '')+'<Span '+Span+' i='+i+'>'+DT[i]+'</Span>';
	}

D.write('<TABLE Class=CalFld Id='+Id+' CELLSPACING=0>'+
	'<tr><td Class="CalFld'+(Val ? 'Data' : 'None')+(!bAndr ? '' : ' Andr')+'"'+
		' OnContextMenu="return false" OnSelectStart="return false">&nbsp;'+Text+'&nbsp;'+
	'<Input Name='+Name+' Value="'+Val+'" Type=Hidden></td><td>');
if (!W.Cal_ImgDir) W.Cal_ImgDir='/images/';

Button(!Cal_DataImg() ? Cal_ImgDir+'arrowdown.gif' : 'data:image/gif;base64,R0lGODlhDAALALMAAAAAANTSzAASAAAAAGAACwEAmhUA0wAAd+jMAPzCABUSAAAAAAAcAQDDAAASAAAAACH5BAEAAAAALAAAAAAMAAsAAwQQMMhJq704W8A57hqgjWSJRQA7', 'Cal_Open', 12, 11, 1, Name);
D.write('</td></tr></TABLE>');

Cal=getbyid(Id);
Cal.date=new Date(DT[2], (Val ? DT[oCal.mi]-1 : 0), DT[oCal.di], 12);

Cal.fld=Cal.getElementsByTagName('INPUT')[0];
oCal.Min=MinYear; oCal.Max=MaxYear;
setTimeout('Cal_Correct("'+Id+'", "'+Val+'")', 100);
}

//--

function Cal_DataImg(){
return (IEVer==0 || (IEVer>=8)); // && !Quirks
}

function Cal_Correct(Id, DT1){
//Restore display after "Back"
var Cal=getbyid(Id), DT;

DT=Cal.fld.value; if (DT==DT1) return;
Cal_SetDate(Cal.fld, DT);
//DT=DT.split(Cal_Delim); Cal_StoreDate(Cal, +DT[oCal.di], DT[], DT[2], true);
}

function Cal_StoreDate(Cal, d, m, y, NoFld, NoObj){
var Parts, dt, OldVal, Fld=Cal.fld;

if (!NoFld){
	OldVal=Fld.value; Fld.value=Cal_DTFmt(d, m, y); 
	if (Fld.onchange && OldVal!=Fld.value) Fld.onchange();
	}

Parts=Cal_GetParts(Cal);
Cal_SetPart(Parts[oCal.di], d); Cal_SetPart(Parts[oCal.mi], m); Cal_SetPart(Parts[2], y);

if (!NoObj){
	Cal.date=new Date(y, m-1, d, 12);
	//dt=Cal.date; dt.setFullYear(y); dt.setMonth(m-1); dt.setDate(d); 
	Fld.parentNode.className='CalFld'+(y.charAt(0)=='0' ? 'None' : 'Data');
	}
}

function Cal_FldMouse(e){
var T, i, dir, Cal, date; EventInit(e); T=E.type;

if (EvSrc.parentNode.className!='CalFldData' || oCal.Shown) return;

if (T=='mouseover'){	
	if (oCal.CurrFld) EvSrc.style.color='';
	EvSrc.style.color='blue';	oCal.CurrFld=EvSrc; //W.status=oCal.Help;
	}
else if (T=='mouseout'){
	EvSrc.style.color=''; oCal.CurrFld=null; //W.status='';
	}
else if (T=='mousedown' || (T=='mouseup' && oCal.lastmouse==T)){
	if (LeftBtn()) dir=1; else dir=-1;
	if (E.shiftKey) dir=-dir;
	
	Cal=Cal_FindCal(EvSrc); date=Cal.date; i=EvSrc.getAttribute('i');
	if (i==2){
		Y=Cal_ChangeYear(date.getFullYear(), dir); if (!Y) return;
		date.setFullYear(Y);
		}
	else if (i==oCal.mi) date.setMonth(date.getMonth()+dir);
	else if (i==oCal.di) date.setDate(date.getDate()+dir);

	Cal_StoreDate(Cal, date.getDate(), date.getMonth()+1, date.getFullYear(),	false, true);
	}
oCal.lastmouse=T;

return Op;
}

function Cal_SetPart(Part, Val){
Part.childNodes[0].data=Cal_Pad(Val);
}

function Cal_GetParts(Cal){
if (!Cal.parts) Cal.parts=Cal.getElementsByTagName('SPAN');
return Cal.parts;
}

function Cal_Create(){

D.write('<Table CellSpacing=0 Name=Calendar Id=Calendar '+
	'OnContextMenu="return false"><TR><TD Height1="100%" VALIGN=Top>'+
	//Height="100%" here may cause the calendar become as high as the window in IE8
	//when you select year
	'<TABLE Height1="100%"><TR><TD Class=CalDays Id=CalDaysHolder>'+
	Cal_DaysTbl()+'</TD></TR><TR><TD Height=1>'); Cal_YearBox();
D.write('</TD></TR></TABLE></TD><TD>'+Cal_MonthList()+'</TD></TR></Table>');

this.Cal=getbyid('Calendar');
this.DaysTbl=getbyid('CalDaysHolder').getElementsByTagName('TABLE')[0];
this.F=Cal_FindForm(this.Cal); this.Shown=false;
//this.Help='Left button - next, Right button - previous, Shift - inverse';
this.d0='00'+Cal_Delim+'00'+Cal_Delim+'0000';
var di=0, mi=1; if (Cal_MD){di=1; mi=0;}
this.di=di; this.mi=mi;
}

function Cal_DaysTbl(){
var Week, i, Text, Buffer, Cell;

Week='Lu,Ma,Mi,Ju,Vi,Sa';
if (!W.Cal_MonFst) Week='Do,'+Week; else Week+=',Do';
Week=Week.split(',');

Text='<Table CellSpacing=0><TR Class=Week>';
//Week <Col Width="10%" Span=7>
for(i=0; i<7; i++) Text+='<TD>'+Week[i]+'</TD>';
Text+='</TR>'; 
//With iOs you have to set handlers directly on the elements
Cell='<TD'+(!iOs ? '' : ' OnMouseDown="Cal_OMD(event)"')+'>.</TD>';
//Days
for(var RowNo=0; RowNo<6; RowNo++){
	Buffer='';
	for(i=0; i<7; i++) Buffer+=Cell;
	Text+=('<TR>'+Buffer+'</TR>');
	}
Text+='</TR></Table>'; return Text;
}

function Cal_Update(YDelta){
var Year;

Year=+oCal.F.Year.value;
if (YDelta!=0){
	Year=Cal_ChangeYear(Year, YDelta); if (!Year) return;
	oCal.F.Year.value=Year;
	}
Cal_Draw(oCal.Day, oCal.F.MonSel.value, Year);
}

function Cal_ChangeYear(Y, Delta){
var Test;

Test=Cal_BadYear(Y);
//already bad - set closest
if (Test) return (Test<0 ? oCal.Min : oCal.Max);
//try to change
var NextY=Y+=Delta; return (Cal_BadYear(NextY) ? 0 : NextY);
}

function Cal_BadYear(Y){
//-1 / 0 / 1, 0 means good
return (oCal.Min && Y<oCal.Min ? -1 : (oCal.Max && Y>oCal.Max ?  1 : 0));
}

function Cal_YearBox(){

D.write('<Table CellSpacing=0 Width="100%"><TR><TD Align=Center Style="font-size:0px;width:1px;">');
Button(!Cal_DataImg() ? Cal_ImgDir+'arrowleft.gif' : 'data:image/gif;base64,R0lGODlhCwALALMAAAQCBNTSzAASAAAAAGgACwEAmhUA0wAAdwjMABbCABYSAAAAAAAcAQDDAAASAAAAACH5BAEAAAAALAAAAAALAAsAAwQRMMhJq71Yggz27d4XfllpYhEAOw==', 'Cal_PrevY', 11, 11);
D.write('</TD><TD Align=Center><Input Name=Year ReadOnly Disabled></TD><TD Align=Center Style="font-size:0px;width:1px;">');
Button(!Cal_DataImg() ? Cal_ImgDir+'arrowright.gif' : 'data:image/gif;base64,R0lGODlhCwALALMAAAQCBNTSzAASAAAAAGAACwEAmhUA0wAAd4jMAI7CABUSAAAAAAAcAQDDAAASAAAAACH5BAEAAAAALAAAAAALAAsAAwQSMMhJq704gKw3BuDnXSNnnlMEADs=', 'Cal_NextY', 11, 11);
D.write('</TD></TR></table>');
}

function Cal_MonthList(){
var Months=("Enero,Febrero,Marzo,Abril,Mayo,Junio,Julio,Agosto,Septiembre,Octubre"+
	",Noviembre,Diciembre").split(','),
	MonSel='<Select Name=MonSel OnChange="Cal_Update(0)" ondblclick="Cal_Close(true)" Size=12 Disabled>', i, DT;

for(i=0; i<12; i++) MonSel+='<Option Value='+i+'>'+Months[i]+'</Option>';
MonSel+='</Select>'; return MonSel;
}

function Cal_Draw(Day, Month, Year){
var DT, ToDay, MonLen, Dow, i, SelDay, Cl, Rows, Row, RowNo=1, TD, bThisMon;

Rows=oCal.DaysTbl.getElementsByTagName('TR');
//init
DT=new Date(); if (!Day) ToDay=DT.getDate();
DT.setDate(1); DT.setHours(12, 0, 0, 0);

if (Year) DT.setFullYear(Year); else Year=DT.getFullYear();
if (Month!=null) DT.setMonth(Month); else Month=DT.getMonth();
SelDay=(Day ? Day : ToDay); MonLen=Cal_GetMonthLen(DT);

//prev month
Dow=DT.getDay();
if (W.Cal_MonFst){
	if (Dow==0) Dow=6; else Dow--;
	}

bThisMon=(aToday[1]==+Month+1 && aToday[2]==Year); ToDay=aToday[0]; Row=Rows[RowNo].childNodes;
for(i=0; i<Dow; i++) Cal_TD(Row[i], nbsp);

//This month
oCal.DayExists=false;
for(i=1; i<=MonLen; i++){
	if (i!=SelDay) Cl='';
	else{
		Cl='SelDay'; oCal.DayExists=true;
		}
	if (bThisMon && ToDay==i) Cl+=' CalToday';
	
	Cal_TD(Row[Dow], i, 'Day_'+i, Cl); Dow++;
	if (Dow>=7){
		Dow=0; RowNo++; Row=Rows[RowNo].childNodes;
		}
	}

//Next month
while(true){
	if (Dow>=7){
		RowNo++; if (RowNo>=7) break;
		Dow=0; Row=Rows[RowNo].childNodes;
		}
	Cal_TD(Row[Dow], nbsp); Dow++;
	}
	
TD=Rows[6].cells[6]; TD.firstChild.data='x'; TD.className='CalClear'; 
TD.onclick=Cal_Clear; TD.title='Clear the date';
}

function Cal_Clear(){
Cal_SetDate(oCal.Fld, ''); Cal_Close();
}

function Cal_TD(TD, Val, id, Cl){

TD.childNodes[0].nodeValue=Val; 
if (id==null) id=''; 
TD.id=id;	
if (Cl==null) Cl='';
TD.className=Cl;
}

function Cal_Toggle(Button){
var CalS=oCal.Cal.style;

if (!oCal.Shown){
	var aPos, y, m, d, DT, Cmd, Fld, Cal;
	
	Cal=Cal_FindCal(Button); Fld=Cal.fld;
	if (Fld){
		DT=Fld.value; if (!DT) DT=Cal_TodayStr();
		DT=DT.split(Cal_Delim); y=DT[2]; m=DT[oCal.mi]-1; d=+DT[oCal.di];
		}
	Cal_Draw(d, m, y); oCal.Fld=Fld; oCal.Day=d; aPos=Cal_GetPos(Cal);

	CalS.left=aPos[0]+'px'; CalS.top=aPos[1]+'px';
	CalS.visibility='visible'; oCal.Shown=true;

	Cmd=(!iOs ? 'CalSetH("onmousedown", Cal_OMD, D.body); ' :
		'CalSetH("ontouchstart", Cal_OMD, D.body); ')+
		'oCal.OKP=D.onkeydown; D.onkeydown=Cal_OKP; oCal.F.MonSel.value='+m+'; ';
	oCal.F.Year.disabled=false; oCal.F.MonSel.disabled=false; oCal.F.Year.value=y; 

	if (Op){
		Cells=oCal.Cal.childNodes[0].childNodes[0].childNodes;
		oCal.Cal.style.width=Cells[0].offsetWidth+Cells[1].offsetWidth+10+'px';
		}
	setTimeout(Cmd, 100);	oCal.But=Button;
	}
else{
	oCal.F.Year.disabled=true; oCal.F.MonSel.disabled=true;
	CalS.visibility='hidden'; CalS.left='0px'; CalS.top='0px';
	oCal.Shown=false; oCal.But=null;

	if (!iOs) CalRemH("onmousedown", Cal_OMD, D.body);
	else CalRemH("ontouchstart", Cal_OMD, D.body);
	D.onkeydown=oCal.OKP; W.focus();
	}
}

//--

function Log(s){
//getbyid('ta').value+=s+"\n";
}

function Cal_OMD(e){
var Day, Last;

EventInit(e); 
if (!Contains(oCal.Cal, EvSrc)){
	Cal_Close(); return;
	}
if (!Contains(oCal.DaysTbl, EvSrc)) return;

if (EvSrc.parentNode.rowIndex==0) return;
Day=+EvSrc.childNodes[0].data;
if (Day){
	if (oCal.DayExists){
		var o=getbyid('Day_'+oCal.Day); o.className=o.className.replace('SelDay', '');
		}
	EvSrc.className+=' SelDay'; oCal.Day=Day; oCal.DayExists=true;
	if (LeftBtn()) setTimeout('Cal_Close(true)', 100);
	}
}

function Cal_OKP(e){
EventInit(e); if (E.keyCode==27) Cal_Close();
}

//--

function Cal_Open(Up){
var But;

if (!Up){
	//open?!
	if (oCal.Shown) Cal_Close();
	But=EvSrc;
	}
Cal_Toggle(But);
}

function Cal_GetPos(Cal){
var oRoot=GetPosRoot(), ScrTop=oRoot.scrollTop, Above, Below, CalFldH=Cal.offsetHeight, 
	X, Y, Diff, CalH=oCal.Cal.offsetHeight, aPos;

aPos=AbsPos(Cal); X=aPos[0]; Y=aPos[1];
//Y
Diff=ScrTop+oRoot.clientHeight-Y-CalFldH-CalH;
if (Diff<0){ //doesn't fit below
	Above=Y-ScrTop;
	if (Above>=CalH) Y-=CalH;
	else{ //doesn't fit above
		Below=Diff+CalH;
		if (Above<Below) Y+=CalFldH+Diff;
		else Y-=Above;
		}
	}
else Y+=CalFldH;

//X
Diff=oRoot.scrollLeft+oRoot.clientWidth-X-oCal.Cal.offsetWidth;
if (Diff<0) X+=Diff;

return [X, Y];
}

function Cal_Close(Save){
var Fld;

if (Save){
	if (!oCal.DayExists) return;
	Fld=oCal.Fld;
	Cal_StoreDate(Cal_FindCal(Fld), oCal.Day, +oCal.F.MonSel.value+1, oCal.F.Year.value);
	}
Button_Toggle(oCal.But, true); Cal_Toggle();
}

//--

function Cal_GetMonthLen(pDT){
//Get number of days for the month in pDT
var DT=new Date(pDT), Mon;

//clear
DT.setDate(1); DT.setHours(0, 0, 0, 0);
//next month
Mon=DT.getMonth()+1;
if (Mon>=12){
	Mon=0; DT.setFullYear(DT.getFullYear()+1);
	}
DT.setMonth(Mon);
//a little back
DT.setHours(-12); return DT.getDate();
}

function Cal_PrevY(){Cal_Update(-1);}
function Cal_NextY(){Cal_Update(1);}

function Cal_FindForm(o){
while(o && o.tagName!='FORM') o=o.parentNode;
return o;
}

function Cal_FindCal(o){
while(o && o.className!='CalFld') o=o.parentNode;
return o;
}

function Cal_Pad(s){
if (typeof(s)=='number') s=s.toString();
return (s.length>1 ? s : '0'+s);
}

function Cal_DTFmt(d, m, y){
return (y>0 ? (Cal_MD ? Cal_Pad(m)+Cal_Delim+Cal_Pad(d) : Cal_Pad(d)+Cal_Delim+Cal_Pad(m))+Cal_Delim+y : '');
}

function Cal_InitVal(Val){
var M;

if (!Val) Val=oCal.d0;
else if (Val=='today') Val=Cal_TodayStr();
else if (Val && Val.charAt(2)!=Cal_Delim){ //YYYYMMDD, YYYY.MM.DD, YYYY-MM-DD -> DD.MM.YYYY
	if (M=Val.match(/(\d{4})[.-]?(\d{2})[.-]?(\d{2})/)) Val=Cal_DTFmt(M[3], M[2], M[1]);
	else if (M=Val.match(/(\d{2})[./-](\d{2})[./-](\d{4})/))
		Val=Cal_DTFmt(M[oCal.di+1], M[oCal.mi+1], M[3]);
	else Val=oCal.d0;
	}
return Val;
}

function Cal_ReverseVal(Fld){
var DT=Fld.value;

if (DT==oCal.d0) return '';
return (DT ? DT.substr(6)+DT.substr(3, 2)+DT.substr(0, 2) : DT);
}

function Cal_ReverseDT(DT){
return DT.getFullYear()+Cal_Pad(DT.getMonth()+1)+Cal_Pad(DT.getDate());
}

function Cal_AddMinutes(Fld, Minutes){
//Note: the initial time is always 12:00
var Cal=getbyid(Fld.name+'_Cal'), date=Cal.date;
date.setMinutes(Minutes+date.getMinutes());
Cal_StoreDate(Cal, date.getDate(), date.getMonth()+1, date.getFullYear(),	false, true);
}

function Cal_IsField(oFld){
var Id=oFld.name+'_Cal'; return getbyid(Id); //+'_Button'
}

//--
//Interface

function Cal_SetRange(MinYear, MaxYear){

if (!oCal) return;
oCal.Min=MinYear; oCal.Max=MaxYear;
}

function Cal_Disable(Fld, Dis){
var Par, NewCl='';

if (Fld.disabled==Dis) return; 
Fld.disabled=Dis; Par=Fld.parentNode;
Class=['CalFldData', 'CalFldDis']; Ind=(Dis ? 0 : 1);
if (Par.className==Class[Ind]) Par.className=Class[1-Ind];
Button_Disable(getbyid(Fld.name+'_Button'), Dis);
}

function Cal_Today(){

if (!W.aToday){
	var DT=new Date; aToday=[DT.getDate(), DT.getMonth()+1, DT.getFullYear()];
	}
return aToday;
}

function Cal_TodayStr(){
var DT=Cal_Today(); return Cal_DTFmt(DT[0], DT[1], DT[2]);
}

function Cal_SetDate(Fld, DT){

if (!DT){
	Fld.value=''; DT=Cal_InitVal(DT);
	}
else{
	DT=Cal_InitVal(DT); Fld.value=DT;
	}
DT=DT.split(Cal_Delim);
Cal_StoreDate(getbyid(Fld.name+'_Cal'), +DT[oCal.di], DT[oCal.mi], DT[2], true);
}

function Cal_BadFld(Fld, Msg, bConfirm){
var Cal=getbyid(Fld.name+'_Cal'); ScrollTo(Cal); 
if (Fld.value) Cal=Cal.rows[0].cells[0];
return BadField(Cal, Msg, true, bConfirm);
}

//--- from formex.js

var _BadField;

function BadField(Field, Msg, NoFocus){
var OldColor;

if (Msg==null) Msg='Please fill in the field before submitting the form';
OldColor=Field.style.color;
Field.style.color='#B22222';
if (!NoFocus) Field.focus(); 
alert(Msg); _BadField=Field; 
setTimeout("_BadField.style.color='"+OldColor+"';", 250);
return false;
}

//--- button.js

var _But_Count=0;

function Button(Img, Act, W, H, Toggle, Pref){
var Text, On, Id;

Id=(Pref ? Pref+'_Button' : 'Button_'+(_But_Count)); _But_Count++;
if (!W) W=16; if (!H) H=16;
On='="Button_Mouse(this, event, '+Act+(Toggle ? ', 1' : '')+')"';

Text='<Div Style="border:1px solid #BEBEBE;border-right-color:#7F7F7F;border-bottom-color:#7F7F7F;font-size:0px;">'+
	'<IMG Id='+Id+' SRC="'+Img+'" '+
	'Style="border:1px solid white;border-right-color:#3F3F3F;border-bottom-color:#3F3F3F;width:'+W+'px;height:'+
	(H+(Op ? 2 : 0))+'px;background-color:black;"'+
	' OnMouseDown'+On+' OnMouseUp'+On+'></Div>';

D.write(Text); return D.getElementById(Id);
}

function Button_Disable(B, Dis){
var Val;

if (Dis==null) Dis=true; Val=(Dis ? 1 : 0);
if (B.dis==Val) return;
B.dis=Val;
//if (!Op && !Gec) B.style.filter=(Dis ? 'gray(), Alpha(opacity=60)' : ''); else 
B.style.backgroundColor=(Dis ? 'gray' : 'black');
}

function Button_Mouse(o, e, Act, Toggle){
var Up;

EventInit(e);
if (o.dis || !LeftBtn()) return;
Up=(E.type=='mouseup');

if (!Up){
	if (Toggle && o.pressed) Up=true;
	}
else if (Toggle) return;

Button_Toggle(o, Up);
if (Up || Toggle) Act(Up);
}

function Button_ToggleAny(St){
var T;

T=St.borderRightColor;
St.borderRightColor=St.borderBottomColor=St.borderLeftColor;
St.borderLeftColor=St.borderTopColor=T;
}

function Button_Toggle(o, Up){
var Cfg=[['1px', '0px'], ['0px', '1px']], Data, St=o.style, DivSt=o.parentNode.style, T;

if (Up || !o.pressed){
	Data=Cfg[!Up ? 0 : 1]; 
	Button_ToggleAny(St); Button_ToggleAny(DivSt);
	
	DivSt.marginLeft=DivSt.marginTop=Data[0];
	DivSt.borderBottomWidth=DivSt.borderRightWidth=Data[1];
	
	T=St.borderTopColor;
	St.borderTopColor=St.borderLeftColor=St.borderTopColor;
	St.borderTopColor=St.borderLeftColor=T;
	}

o.pressed=(!Up);
} 

//--- scrollto.js

function ScrollTo(O, Where, NoFocus){
//Where={top, bottom, middle}
var Pos, Y, ObjH=O.offsetHeight, WinH=D.body.clientHeight, oRoot=GetPosRoot();

Pos=AbsPos(O); Y=Pos[1];
if (Where==null){
	if (Y<oRoot.scrollTop) Where='top';
	else if (Y+ObjH>oRoot.scrollTop+WinH) Where='bottom';
	}

if (Where=='middle'){
	if (ObjH>WinH) Where='top';
	else W.scrollTo(0, Y-Math.round((WinH-ObjH)/2));
	}
if (Where=='top') W.scrollTo(0, Y-1);
else if (Where=='bottom') W.scrollTo(0, Y+ObjH-WinH);

if (NoFocus!=true) W.focus(); return false;
}

function CalSetH(Ev, F, O){
if (O.attachEvent) O.attachEvent(Ev, F); else O.addEventListener(Ev.substr(2), F, false);
}

function CalRemH(Ev, F, O){
if (O.attachEvent) O.detachEvent(Ev, F); else O.removeEventListener(Ev.substr(2), F, false);
}

