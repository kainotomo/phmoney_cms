import{_ as x,r as g,o as n,g as l,a as s,k as h,v as V,t as u,b as _,w as v,p as f,F as d,m,n as U,e as A,i as S,d as D,c as I}from"./main.e98d045c.js";const B={async created(){await this.store.get("/phmoney/import/transactions-from-csv/page3"),this.form={upload_file:this.store.props.upload_file,file_path:this.store.props.file_path,items:[...this.store.props.items],delimiter:this.store.props.delimiter,enclosure:this.store.props.enclosure,date_format:this.store.props.date_format,currency_format:this.store.props.currency_format,selected_columns:[...this.store.props.selected_columns],skip_errors:null},this.accounts=[...this.store.props.accounts]},data(){return{form:{upload_file:null,file_path:null,items:[],selected_columns:[],skip_errors:null},accounts:[],source_account:null,destination_account:null,columns:["Date","Num","Description","Amount","Shares"],savingSettings:!1}},methods:{next(){confirm("Are you sure you want to proceed to import selected items?")&&this.$router.push({name:"import.transactions_from_csv.page4"})},async submit(){await this.store.post("/phmoney/import/transactions-from-csv/page3/update",this.form),await this.store.get("/phmoney/import/transactions-from-csv/page3"),this.reset()},reset(){for(let o=0;o<this.form.items.length;o++)this.form.items[o].checked=this.store.props.items[o].checked,this.form.items[o].is_valid=this.store.props.items[o].is_valid,this.form.items[o].validation_message=this.store.props.items[o].validation_message},getTrValidClass(o){return o.is_valid===!1&&o.checked===!0?"bg-red-500":""},onCheckAll(o){let t=o.target.checked;for(let p=0;p<this.form.items.length;p++)this.form.items[p].checked=t;this.submit()},onSourceAccountChange(){for(let o=0;o<this.form.items.length;o++)this.form.items[o].source_account=this.source_account;this.submit()},onDestinationAccountChange(){for(let o=0;o<this.form.items.length;o++)this.form.items[o].destination_account=this.destination_account;this.submit()}}},N={key:0,class:"p-6"},T={class:"flex items-center justify-end mt-4 gap-2"},F={class:"text-sm text-red-600"},L=s("span",{class:"material-icons-outlined"},"navigate_before",-1),P=s("span",{class:"material-icons-outlined"},"navigate_next",-1),M={class:"overflow-auto h-screen"},j={class:"table"},z=["colspan"],E=s("th",null,null,-1),R=["value"],q={class:"w-60"},G=["value"],H=s("th",{scope:"col"},"#",-1),J=["onUpdate:modelValue"],K=["value"],O={key:0,class:"text-white p-2"},Q={key:1},W=["id","onUpdate:modelValue"],X=["value"],Y=["id","onUpdate:modelValue"],Z=["value"],$={scope:"row"},ee={class:"p-2 border"};function se(o,t,p,y,r,a){const b=g("form-secondary-button"),C=g("form-button"),w=g("form-label"),k=g("form-checkbox");return r.form.items.length>0?(n(),l("div",N,[s("form",{onSubmit:t[9]||(t[9]=A(()=>{},["prevent"]))},[s("div",T,[h(s("div",null,[s("p",F,u(o.store.props.can_proceed_message),1)],512),[[V,o.store.props.can_proceed_message]]),_(b,{onClick:t[0]||(t[0]=e=>o.$router.back()),title:"Back"},{default:v(()=>[L]),_:1}),_(C,{disabled:r.form.processing||!o.store.props.can_proceed,onClick:a.next,title:"Next"},{default:v(()=>[P]),_:1},8,["disabled","onClick"]),_(w,{for:"skip_errors",value:"Skip Invalid"}),_(k,{id:"skip_errors",name:"skip_errors",modelValue:r.form.skip_errors,"onUpdate:modelValue":t[1]||(t[1]=e=>r.form.skip_errors=e),onChange:a.submit},null,8,["modelValue","onChange"])]),s("div",M,[s("table",j,[s("thead",null,[s("tr",null,[s("th",{colspan:r.form.items[0].value.length+4}," Total Rows - "+u(r.form.items.length),9,z)]),s("tr",null,[E,s("th",null,[_(k,{id:"index_check_all",name:"index_check_all",onInput:a.onCheckAll},null,8,["onInput"])]),s("th",null,[h(s("select",{id:"source_account_all","onUpdate:modelValue":t[2]||(t[2]=e=>r.source_account=e),onChange:t[3]||(t[3]=(...e)=>a.onSourceAccountChange&&a.onSourceAccountChange(...e)),class:"max-w-sm border-gray-300 focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50 rounded-md shadow-sm"},[(n(!0),l(d,null,m(r.accounts,e=>(n(),l("option",{key:e.guid,value:e},u(e.name),9,R))),128))],544),[[f,r.source_account]])]),s("th",null,[s("div",q,[h(s("select",{id:"destination_account_all","onUpdate:modelValue":t[4]||(t[4]=e=>r.destination_account=e),onChange:t[5]||(t[5]=(...e)=>a.onDestinationAccountChange&&a.onDestinationAccountChange(...e)),class:"max-w-sm border-gray-300 focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50 rounded-md shadow-sm"},[(n(!0),l(d,null,m(r.accounts,e=>(n(),l("option",{key:e.guid,value:e},u(e.name),9,G))),128))],544),[[f,r.destination_account]])])]),H,(n(!0),l(d,null,m(r.form.items[0].value.length-1,e=>(n(),l("td",null,[h(s("select",{"onUpdate:modelValue":c=>r.form.selected_columns[e-1]=c,onChange:t[6]||(t[6]=c=>a.submit()),class:"max-w-sm border-gray-300 focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50 rounded-md shadow-sm"},[(n(!0),l(d,null,m(r.columns,c=>(n(),l("option",{value:c},u(c),9,K))),256))],40,J),[[f,r.form.selected_columns[e-1]]])]))),256))])]),s("tbody",null,[(n(!0),l(d,null,m(r.form.items,(e,c)=>(n(),l("tr",{class:U(a.getTrValidClass(e))},[e.is_valid===!1&&e.checked===!0?(n(),l("td",O,u(e.validation_message),1)):(n(),l("td",Q)),s("td",null,[_(k,{id:"index_check"+c,name:"index_check",checked:e.checked,"onUpdate:checked":i=>e.checked=i,onChange:a.submit},null,8,["id","checked","onUpdate:checked","onChange"])]),s("td",null,[h(s("select",{id:`source_account${c}`,"onUpdate:modelValue":i=>e.source_account=i,onChange:t[7]||(t[7]=(...i)=>a.submit&&a.submit(...i)),class:"max-w-sm border-gray-300 focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50 rounded-md shadow-sm"},[(n(!0),l(d,null,m(r.accounts,i=>(n(),l("option",{key:i.guid,value:i},u(i.name),9,X))),128))],40,W),[[f,e.source_account]])]),s("td",null,[h(s("select",{id:`destination_account${c}`,"onUpdate:modelValue":i=>e.destination_account=i,onChange:t[8]||(t[8]=(...i)=>a.submit&&a.submit(...i)),class:"max-w-sm border-gray-300 focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50 rounded-md shadow-sm"},[(n(!0),l(d,null,m(r.accounts,i=>(n(),l("option",{key:i.guid,value:i},u(i.name),9,Z))),128))],40,Y),[[f,e.destination_account]])]),s("td",$,u(c),1),(n(!0),l(d,null,m(e.value,i=>(n(),l("td",ee,u(i),1))),256))],2))),256))])])])],32)])):S("",!0)}var oe=x(B,[["render",se]]);const te={class:"bg-white shadow mt-4 prose max-w-none"},re=D({name:"TransactionsFromCsvPage3",setup(o){return(t,p)=>{const y=g("ImportLayout");return n(),I(y,{title:"Import Preview"},{default:v(()=>[s("div",te,[_(oe)])]),_:1})}}});export{re as default};