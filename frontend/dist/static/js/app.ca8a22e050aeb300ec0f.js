webpackJsonp([1],{"0CEO":function(t,e){},"29bF":function(t,e){},"7zck":function(t,e){},AtBM:function(t,e){},CqdX:function(t,e){},NHnr:function(t,e,n){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var i=n("7+uW"),o={render:function(){var t=this.$createElement,e=this._self._c||t;return e("v-app",[e("v-main",[e("router-view")],1)],1)},staticRenderFns:[]};var a=n("VU/8")({name:"App"},o,!1,function(t){n("AtBM")},null,null).exports,l=n("/ocq"),r=n("c/Tr"),s=n.n(r),c=n("Dd8w"),d=n.n(c),v=n("gRE1"),u=n.n(v),m=n("mvHQ"),h=n.n(m),f=n("mtWM"),p=n.n(f),g={props:{time:Date,booked:Boolean,selected:Boolean,isOwner:Boolean,isAdmin:Boolean,guidanceEmail:String,emailColor:{default:function(){return"blue"},type:String}},computed:{date:function(){return this.time.toISOString().split("T")[0]},timeStart:function(){var t=this.time.toISOString().split(".")[0].split("T")[1];return t.substring(0,t.length-3)},timeEnd:function(){var t=new Date(this.time.getTime()+18e5).toISOString().split(".")[0].split("T")[1];return t.substring(0,t.length-3)},color:function(){return this.selected?"purple lighten-2":this.isOwner?"green lighten-2":this.booked?"grey lighten-2":this.emailColor}}},_={render:function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("v-timeline-item",{attrs:{color:t.color,"fill-dot":"",small:!t.selected}},[n("v-card",{staticClass:"mx-auto",attrs:{color:t.color}},[t.booked&&!t.isOwner?n("v-card-title",{staticClass:"title"},[n("v-container",{staticClass:"time-card"},[n("v-row",[n("v-col",{attrs:{cols:"12",md:"12"}},[n("h2",{staticClass:"white--text font-weight-light"},[n("v-icon",{staticClass:"mr-4",attrs:{dark:"",size:"42"}},[t._v(" mdi-calendar-clock ")]),t._v("\n                          "+t._s(t.date)+"\n                      ")],1),t._v(" "),n("h3",{staticClass:"white--text font-weight-light"},[t._v("\n                          "+t._s(t.timeStart)+" - "+t._s(t.timeEnd)+"\n                      ")])])],1)],1)],1):n("v-card-title",{staticClass:"title"},[n("v-container",{staticClass:"time-card"},[n("v-row",[n("v-col",{attrs:{cols:"12",md:"12"}},[n("h2",{staticClass:"white--text font-weight-light"},[n("v-icon",{staticClass:"mr-4",attrs:{dark:"",size:"42"}},[t._v(" mdi-calendar-clock ")]),t._v("\n                          "+t._s(t.date)+"\n                      ")],1),t._v(" "),n("h3",{staticClass:"white--text font-weight-light"},[t._v("\n                          "+t._s(t.timeStart)+" - "+t._s(t.timeEnd)+"\n                      ")]),t._v(" "),n("v-container",{directives:[{name:"show",rawName:"v-show",value:t.selected,expression:"selected"}]},[n("v-row",[n("v-col",{staticClass:"white--text font-weight-light"},[t._v("\n                                  "+t._s(t.$locale.timeSlot_bookEmail)+":\n                                  "),n("a",{staticClass:"white--text font-weight-light",attrs:{href:"mailto:"+t.guidanceEmail}},[t._v("\n                                      "+t._s(t.guidanceEmail)+"\n                                  ")])])],1),t._v(" "),n("v-row",[n("v-col",[n("p",[!t.booked&&t.isAdmin?n("v-btn",{staticClass:"mr-4",on:{click:function(e){return e.stopPropagation(),e.preventDefault(),t.$emit("remove")}}},[t._v("\n                                      "+t._s(t.$locale.timeSlot_removeButton)+"\n                                  ")]):t._e()],1),t._v(" "),t.isOwner?n("v-btn",{staticClass:"mr-4",on:{click:function(e){return e.stopPropagation(),e.preventDefault(),t.$emit("cancel")}}},[t._v("\n                                      "+t._s(t.$locale.timeSlot_cancelButton)+"\n                                  ")]):n("v-btn",{staticClass:"mr-4",on:{click:function(e){return e.stopPropagation(),e.preventDefault(),t.$emit("book")}}},[t._v("\n                                      "+t._s(t.$locale.timeSlot_bookButton)+"\n                                  ")])],1)],1)],1)],1)],1)],1)],1)],1)],1)},staticRenderFns:[]};var w=n("VU/8")(g,_,!1,function(t){n("0CEO")},"data-v-52f984ca",null).exports,k={props:{show:{type:Boolean,default:function(){return!0}},alertMsg:String,color:{type:String,default:function(){return"blue"}},title:String}},S={render:function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("v-dialog",{attrs:{"z-index":10,value:t.show,dark:!1,"max-width":"1000px",scrollable:"",light:""}},[n("v-card",{staticClass:"mx-auto",attrs:{color:t.color+" lighten-1"}},[n("v-card-title",{staticClass:"title"},[n("h2",{staticClass:"white--text font-weight-light"},[t._v("\n            "+t._s(t.title)+"\n            ")])]),t._v(" "),n("v-card-text",{staticClass:"white text--primary"},[n("v-container",[n("v-row",[n("v-col",[n("br"),t._v(" "),n("h2",{staticClass:"font-weight-light"},[n("p",[t._v(t._s(t.alertMsg))])])])],1),t._v(" "),n("v-row",[n("v-col",[t._t("content")],2)],1),t._v(" "),n("v-row",[n("v-col",[t._t("buttons"),t._v(" "),n("v-btn",{staticClass:"white--text",attrs:{color:"grey"},on:{click:function(e){return t.$emit("close")}}},[t._v("\n                        "+t._s(t.$locale.alert_closeButton)+"\n                        ")])],2)],1)],1)],1)],1)],1)},staticRenderFns:[]},b={components:{TimeSlot:w,Alert:n("VU/8")(k,S,!1,null,null,null).exports},data:function(){return{email:null,link:null,isAdmin:!1,errorMsg:"",newSlot:!1,valid:!1,eventsLoaded:!1,eventSlots:[],events:[],activeEvent:{time:"",email:""},myBooking:!1}},computed:{customAttrs:function(){return{email:this.email}},url:function(){var t=window.location.href.split("#")[0].split("?")[0];return t.endsWith("index.php")&&(t=t.replace("index.php","")),t.endsWith("index.html")&&(t=t.replace("index.html","")),t+"api.php"}},methods:{validate:function(t){var e=this;this.$refs.form.validate(),""===this.activeEvent.email&&(this.activeEvent={email:this.myBooking.guidance_email,time:new Date(this.myBooking.date+"T"+this.myBooking.timeStart)});var n=this.checkBooked(this.activeEvent);if(this.valid&&(!n||this.email===n.email)){var i=new FormData,o=this.events.find(function(t){return t.email===e.email});o||(o={email:this.email}),o.name="Event for "+this.email,o.guidance_email=this.activeEvent.email,o.start=this.activeEvent.time.getTime(),o.end=this.activeEvent.time.getTime()+18e5,i.append("event",h()(o)),i.append("action",t),p.a.post(this.url,i).then(function(t){t.data.error?(e.showAlert=!0,e.errorMsg=e.$locale.home_unableToAddEventError+t.data.error):e.$router.push("/confirm")}).catch(function(t){console.error(t)})}},removeSlot:function(t){var e=this;if(this.isAdmin&&!this.checkBooked(t)){var n=new FormData;n.append("timeslots",h()([t.time])),n.append("action","removeTimeslots"),p.a.post(this.url,n).then(function(t){t.data.error?(e.showAlert=!0,e.errorMsg=e.$locale.home_unableToRemoveSlotError+t.data.error):e.$router.push("/confirm")})}},addSlot:function(){var t=this;if(this.newSlot.date&&this.newSlot.time&&this.isAdmin){var e=new FormData,n=this.newSlot.date+"T"+this.newSlot.time+":00.000Z",i=this.newSlot.link;e.append("timeslots",h()([{time:n,link:i}])),e.append("action","addTimeslots"),p.a.post(this.url,e).then(function(e){t.newSlot=!1,e.data.error?(t.showAlert=!0,t.errorMsg=t.$locale.home_unableToAddSlotError+e.data.error):t.$router.push("/confirm")})}},getEvents:function(t){var e=this,n=t.start,i=t.end;p.a.get(this.url+"?start="+n+"&end="+i+"&action=events").then(function(t){t.data&&(e.events=u()(t.data),e.events.sort(function(t,e){return new Date(t.start)-new Date(e.start)}))})},getEventSlots:function(t){var e=this,n=t.start,i=t.end;return p.a.get(this.url+"/?action=eventslots&start="+n+"&end="+i).then(function(t){t.data&&(e.eventSlots=u()(t.data).map(function(t){return d()({},t,{time:new Date(t.time)})}),e.eventSlots.sort(function(t,e){return t.time-e.time}))})},selectTime:function(t){var e=this.checkBooked(t);e&&e.email!==this.email||(this.activeEvent=t)},checkBooked:function(t){var e=new Date(t.time.getTime()+18e5);return this.events.find(function(n){var i=new Date(n.start),o=t.time<new Date(i.getTime()+18e5)&&e>i,a=n.guidance_email===t.email;return o&&a})},emailColor:function(t){return"#"+s()(t.substring(0,6)).map(function(t){return(Math.abs(t.charCodeAt(0)-"a".charCodeAt(0))%16).toString(16)}).join("")}},mounted:function(){var t=this,e=new Date;e.setHours(1,0,0,0);var n=new Date(e);n.setDate(n.getDate()+(this.$locale.bookableDaysForward||7)),p.a.get(this.url+"/?action=loggedin").then(function(e){if(t.email=e.data.email,t.link=e.data.link,t.isAdmin=e.data.admin,t.eventsLoaded=!0,e.data.bookedEvent&&e.data.bookedEvent.length>0){var n=new Date(e.data.bookedEvent[0].start).toISOString().split("T");t.myBooking={date:n[0],guidance_email:e.data.bookedEvent[0].guidance_email,timeStart:n[1].substring(0,"00:00".length),timeEnd:new Date(e.data.bookedEvent[0].end).toISOString().split("T")[1].substring(0,"00:00".length)}}if(!1===e.data.loggedin)throw t.$router.push("/login"),Error("Not admin in")}).then(function(){return t.getEventSlots({start:e.toISOString(),end:n.toISOString()})}).then(function(){return t.getEvents({start:e.toISOString(),end:n.toISOString()})}).catch(function(e){console.error(e),t.$router.push("/login")})}},x={render:function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("v-form",{ref:"form",attrs:{"lazy-validation":""},model:{value:t.valid,callback:function(e){t.valid=e},expression:"valid"}},[n("v-container",[n("v-row",[n("v-col",{attrs:{cols:"12",md:"12"}},[n("div",{staticStyle:{"text-align":"left",padding:"0 50px"}},[n("v-container",[n("v-row",[n("v-col",{attrs:{cols:"12",md:"12"}},[t.myBooking?n("v-card",{staticClass:"mx-auto",attrs:{color:"green lighten-2"}},[n("v-card-title",{staticClass:"title"},[n("v-container",{staticClass:"my-booking-card"},[n("v-row",[n("v-col",{attrs:{cols:"12",md:"12"}},[n("div",{staticClass:"white--text"},[t._v("Min bokning:")]),t._v(" "),n("h2",{staticClass:"white--text font-weight-light"},[n("v-icon",{staticClass:"mr-4",attrs:{dark:"",size:"42"}},[t._v(" mdi-calendar-clock ")]),t._v("\n                            "+t._s(t.myBooking.date)+"\n                          ")],1),t._v(" "),n("h3",{staticClass:"white--text font-weight-light"},[t._v("\n                              "+t._s(t.myBooking.timeStart)+" - "+t._s(t.myBooking.timeEnd)+"\n                          ")]),t._v(" "),t.link?n("div",{staticClass:"white--text"},[t._v("\n                            Mötes länk: "),n("a",{staticClass:"white--text",attrs:{href:t.link,target:"_blank"}},[t._v(t._s(t.link))])]):t._e(),t._v(" "),n("v-btn",{staticClass:"mr-4",on:{click:function(e){return e.stopPropagation(),e.preventDefault(),t.validate("deleteEvent")}}},[t._v("Avboka")])],1)],1)],1)],1)],1):t._e()],1)],1)],1)],1),t._v(" "),n("h3",[t._v(t._s(t.$locale.home_title))]),t._v(" "),n("v-timeline",{attrs:{dense:""}},[t.isAdmin?n("v-sheet",{on:{click:function(e){t.newSlot={}}}},[n("v-timeline-item",{staticClass:"text-left",attrs:{icon:"mdi-plus",color:"green lighten-0"}},[n("h3",{staticClass:"font-weight-light",staticStyle:{"margin-top":"10px",color:"grey"}},[t._v("\n                "+t._s(t.$locale.home_addNewSlotButton)+"\n              ")])])],1):t._e(),t._v(" "),t._l(t.eventSlots,function(e,i){return n("v-sheet",{key:i,on:{click:function(n){return t.selectTime(e)}}},[n("time-slot",{attrs:{booked:!!t.checkBooked(e),"is-admin":t.isAdmin&&e.email===t.email,"is-owner":(t.checkBooked(e)||{}).email===t.email,"guidance-email":e.email,selected:t.activeEvent.time.toString()===e.time.toString()&&t.activeEvent.email===e.email,time:e.time,emailColor:t.emailColor(e.email)},on:{book:function(e){return t.validate("addEvent")},cancel:function(e){return t.validate("deleteEvent")},remove:function(n){return t.removeSlot(e)}}})],1)}),t._v(" "),t.isAdmin?n("v-sheet",{on:{click:function(e){t.newSlot={}}}},[n("v-timeline-item",{staticClass:"text-left",attrs:{icon:"mdi-plus",color:"green lighten-0"}},[n("h3",{staticClass:"font-weight-light",staticStyle:{"margin-top":"10px",color:"grey"}},[t._v("\n                "+t._s(t.$locale.home_addNewSlotButton)+"\n              ")])])],1):t._e()],2)],1)],1)],1),t._v(" "),n("alert",{attrs:{show:!!t.errorMsg,title:t.$locale.home_errorTitle,color:"red","alert-msg":t.errorMsg},on:{close:function(e){t.errorMsg=""}}}),t._v(" "),t.newSlot?n("alert",{attrs:{color:"blue",scrollable:"",title:t.$locale.home_addSlotTitle,"alert-msg":t.$locale.home_addSlotSubTitle},on:{close:function(e){t.newSlot=!1}},scopedSlots:t._u([{key:"content",fn:function(){return[n("v-container",[n("v-row",[n("v-col",[n("v-text-field",{attrs:{label:"Länk till remote möte"},model:{value:t.newSlot.link,callback:function(e){t.$set(t.newSlot,"link",e)},expression:"newSlot.link"}})],1)],1),t._v(" "),n("v-row",[n("v-col",[n("v-date-picker",{staticClass:"theme--light",attrs:{color:"green lighten-1",locale:"swe"},model:{value:t.newSlot.date,callback:function(e){t.$set(t.newSlot,"date",e)},expression:"newSlot.date"}})],1),t._v(" "),n("v-col",[n("v-time-picker",{attrs:{color:"green lighten-1","allowed-minutes":function(t){return t%5==0},format:"24hr"},model:{value:t.newSlot.time,callback:function(e){t.$set(t.newSlot,"time",e)},expression:"newSlot.time"}})],1)],1)],1)]},proxy:!0},{key:"buttons",fn:function(){return[n("v-btn",{staticClass:"white--text",attrs:{color:"teal"},on:{click:function(e){return t.addSlot()}}},[t._v("\n      "+t._s(t.$locale.home_addSlotButton)+"\n      ")])]},proxy:!0}],null,!1,3629555381)}):t._e()],1)},staticRenderFns:[]};var C=n("VU/8")(b,x,!1,function(t){n("29bF")},"data-v-09c3e8d8",null).exports,E={data:function(){var t=this;return{valid:!1,email:"",password:"",emailRules:[function(e){return!!e||t.$locale.login_emailRules_emailRequired1},function(e){return/.+@.+/.test(e)||t.$locale.login_emailRules_emailRequired2}],passwordRules:[function(e){return!!e||t.$locale.login_emailRules_passwordRequired}]}},computed:{url:function(){var t=window.location.href.split("#")[0].split("?")[0];return t.endsWith("index.php")&&(t=t.replace("index.php","")),t.endsWith("index.html")&&(t=t.replace("index.html","")),t+"api.php"}},methods:{validate:function(){var t=this;if(this.$refs.form.validate()&&this.valid){var e=new FormData;e.append("email",this.email),e.append("pwd",this.password),e.append("action","login"),p.a.post(this.url,e).then(function(){t.$router.push("/")}).catch(function(t){console.error(t)})}}}},$={render:function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("v-form",{ref:"form",staticClass:"login-form",attrs:{"lazy-validation":""},model:{value:t.valid,callback:function(e){t.valid=e},expression:"valid"}},[n("v-container",[n("v-row",[n("v-col",[n("h1",[t._v(t._s(t.$locale.login_title))]),t._v(" "),n("v-text-field",{attrs:{rules:t.emailRules,counter:30,label:t.$locale.login_email,required:""},on:{keyup:function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"enter",13,e.key,"Enter")?null:t.validate()}},model:{value:t.email,callback:function(e){t.email=e},expression:"email"}})],1)],1),t._v(" "),n("v-row",[n("v-col",[n("v-text-field",{attrs:{type:"password",rules:t.passwordRules,label:t.$locale.login_password,required:""},on:{keyup:function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"enter",13,e.key,"Enter")?null:t.validate()}},model:{value:t.password,callback:function(e){t.password=e},expression:"password"}})],1)],1),t._v(" "),n("v-row",[n("v-col",[n("v-btn",{staticClass:"mr-4",attrs:{disabled:!t.valid},on:{click:function(e){return t.validate()}}},[t._v("\n          "+t._s(t.$locale.login_loginButton)+"\n        ")])],1)],1)],1)],1)},staticRenderFns:[]};var y=n("VU/8")(E,$,!1,function(t){n("CqdX")},"data-v-83ec9db2",null).exports,B={render:function(){var t=this.$createElement,e=this._self._c||t;return e("v-form",{ref:"form",attrs:{"lazy-validation":""}},[e("v-container",[e("v-row",[e("v-col",[this._v(" "+this._s(this.$locale.confirm_title)+" ")])],1),this._v(" "),e("v-row",[e("v-col",[e("v-btn",{staticClass:"mr-4"},[e("router-link",{attrs:{to:"/"}},[this._v(this._s(this.$locale.confirm_backButton))])],1)],1)],1)],1)],1)},staticRenderFns:[]};var D=n("VU/8")({},B,!1,function(t){n("dwrr")},"data-v-4f8357c3",null).exports;i.default.use(l.a);var T=new l.a({routes:[{path:"/",name:"Home",component:C},{path:"/login",name:"Login",component:y},{path:"/confirm",name:"Confirm",component:D}]}),A=n("3EgV"),O=n.n(A);n("7zck");i.default.use(O.a);var R=new O.a({theme:{dark:!1,themes:{light:{primary:"#3f51b5",secondary:"#b0bec5",accent:"#8c9eff",error:"#b71c1c"}}}}),M={install:function(t,e){t.prototype.$locale=window.defaultLocale}};i.default.config.productionTip=!1,i.default.use(M),new i.default({el:"#app",router:T,vuetify:R,components:{App:a},template:"<App/>"})},dwrr:function(t,e){}},["NHnr"]);
//# sourceMappingURL=app.ca8a22e050aeb300ec0f.js.map