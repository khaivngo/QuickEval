(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-19bd4fc8"],{"12b2":function(t,e,i){"use strict";var n=i("2b0e");e["a"]=n["a"].extend({name:"v-card-title",functional:!0,props:{primaryTitle:Boolean},render:function(t,e){var i=e.data,n=e.props,s=e.children;return i.staticClass=("v-card__title "+(i.staticClass||"")).trim(),n.primaryTitle&&(i.staticClass+=" v-card__title--primary"),t("div",i,s)}})},2677:function(t,e,i){"use strict";i.d(e,"a",function(){return l});var n=i("8654"),s=i("a844"),a=i("7cf7"),o=i("ab6d"),r=i("d9bd"),l={functional:!0,$_wrapperFor:n["a"],props:{textarea:Boolean,multiLine:Boolean},render:function(t,e){var i=e.props,c=e.data,u=e.slots,h=e.parent;Object(o["a"])(c);var d=Object(a["a"])(u(),t);return i.textarea&&Object(r["d"])("<v-text-field textarea>","<v-textarea outline>",l,h),i.multiLine&&Object(r["d"])("<v-text-field multi-line>","<v-textarea>",l,h),i.textarea||i.multiLine?(c.attrs.outline=i.textarea,t(s["a"],c,d)):t(n["a"],c,d)}}},"41f4":function(t,e,i){"use strict";var n=i("ac7c");e["a"]=n["a"]},"60e6":function(t,e,i){"use strict";var n=i("2b0e");e["a"]=n["a"].extend({name:"filterable",props:{noDataText:{type:String,default:"$vuetify.noDataText"}}})},"73cf":function(t,e,i){"use strict";i.r(e);var n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-card",{staticClass:"qe-card pa-5"},[i("v-card-title",{attrs:{"primary-title":""}},[i("div",[i("h3",{staticClass:"headline mb-0"},[t._v("Register")])])]),i("v-text-field",{staticClass:"mt-3",attrs:{label:"Email"},model:{value:t.form.email,callback:function(e){t.$set(t.form,"email","string"===typeof e?e.trim():e)},expression:"form.email"}}),i("v-text-field",{staticClass:"mt-3",attrs:{label:"Password"},model:{value:t.form.password,callback:function(e){t.$set(t.form,"password","string"===typeof e?e.trim():e)},expression:"form.password"}}),i("v-text-field",{staticClass:"mt-3",attrs:{label:"Confirm password"},model:{value:t.form.confirmPassword,callback:function(e){t.$set(t.form,"confirmPassword","string"===typeof e?e.trim():e)},expression:"form.confirmPassword"}}),i("v-text-field",{staticClass:"mt-3",attrs:{label:"First Name"},model:{value:t.form.firstName,callback:function(e){t.$set(t.form,"firstName","string"===typeof e?e.trim():e)},expression:"form.firstName"}}),i("v-text-field",{staticClass:"mt-3",attrs:{label:"Last Name"},model:{value:t.form.lastName,callback:function(e){t.$set(t.form,"lastName","string"===typeof e?e.trim():e)},expression:"form.lastName"}}),i("v-select",{staticClass:"mt-3",attrs:{items:["Female","Male","Other","Rather not say"],label:"Gender"},model:{value:t.form.gender,callback:function(e){t.$set(t.form,"gender",e)},expression:"form.gender"}}),i("v-text-field",{staticClass:"mt-3",attrs:{label:"Year of Birth",placeholder:"yyyy"},model:{value:t.form.yob,callback:function(e){t.$set(t.form,"yob","string"===typeof e?e.trim():e)},expression:"form.yob"}}),i("v-text-field",{staticClass:"mt-3",attrs:{label:"Institution"},model:{value:t.form.institution,callback:function(e){t.$set(t.form,"institution","string"===typeof e?e.trim():e)},expression:"form.institution"}}),i("v-autocomplete",{attrs:{items:t.states,filter:t.customFilter,"item-text":"name",label:"Nationality"},model:{value:t.form.nationality,callback:function(e){t.$set(t.form,"nationality","string"===typeof e?e.trim():e)},expression:"form.nationality"}}),i("v-checkbox",{attrs:{color:"success",label:"Register as scientist"},model:{value:t.form.scientist,callback:function(e){t.$set(t.form,"scientist",e)},expression:"form.scientist"}}),i("p",{staticClass:"caption"},[t._v("Check this box to ")]),i("v-card-actions",[i("v-btn",{staticClass:"mt-5",attrs:{depressed:"",color:"#78AA1C",dark:"",loading:t.registering,large:""},on:{click:t.register}},[t._v("\n      Register\n    ")])],1)],1)},s=[],a=(i("7f7f"),{data:function(){return{form:{email:"",password:"",confirmPassword:"",firstName:"",lastName:"",gender:"",yob:"",institution:"",nationality:"",scientist:!1},registering:!1,states:[{name:"Norway"},{name:"Sweden"}]}},methods:{register:function(){},customFilter:function(t,e,i){var n=t.name.toLowerCase(),s=e.toLowerCase();return n.indexOf(s)>-1}}}),o=a,r=(i("acd9"),i("2877")),l=i("6544"),c=i.n(l),u=i("c6a6"),h=i("8336"),d=i("b0af"),f=i("99d9"),p=i("12b2"),m=i("ac7c"),v=i("b56d"),g=i("2677"),b=Object(r["a"])(o,n,s,!1,null,"6d8ae336",null);e["default"]=b.exports;c()(b,{VAutocomplete:u["a"],VBtn:h["a"],VCard:d["a"],VCardActions:f["a"],VCardTitle:p["a"],VCheckbox:m["a"],VSelect:v["a"],VTextField:g["a"]})},"7e63":function(t,e,i){},"7f7f":function(t,e,i){var n=i("86cc").f,s=Function.prototype,a=/^\s*function ([^ (]*)/,o="name";o in s||i("9e1e")&&n(s,o,{configurable:!0,get:function(){try{return(""+this).match(a)[1]}catch(t){return""}}})},8038:function(t,e,i){},"90bd":function(t,e,i){},"98d8":function(t,e,i){},"99d9":function(t,e,i){"use strict";var n=i("80d2"),s=i("b0af"),a=i("adda"),o=i("d9bd"),r=a["a"].extend({name:"v-card-media",mounted:function(){Object(o["d"])("v-card-media",this.src?"v-img":"v-responsive",this)}}),l=i("12b2");i.d(e,"a",function(){return c}),i.d(e,"b",function(){return u});var c=Object(n["e"])("v-card__actions"),u=Object(n["e"])("v-card__text");s["a"],l["a"]},a844:function(t,e,i){"use strict";i("7e63");var n=i("8654"),s=i("d9bd"),a=Object.assign||function(t){for(var e=1;e<arguments.length;e++){var i=arguments[e];for(var n in i)Object.prototype.hasOwnProperty.call(i,n)&&(t[n]=i[n])}return t};e["a"]={name:"v-textarea",extends:n["a"],props:{autoGrow:Boolean,noResize:Boolean,outline:Boolean,rowHeight:{type:[Number,String],default:24,validator:function(t){return!isNaN(parseFloat(t))}},rows:{type:[Number,String],default:5,validator:function(t){return!isNaN(parseInt(t,10))}}},computed:{classes:function(){return a({"v-textarea":!0,"v-textarea--auto-grow":this.autoGrow,"v-textarea--no-resize":this.noResizeHandle},n["a"].options.computed.classes.call(this,null))},dynamicHeight:function(){return this.autoGrow?this.inputHeight:"auto"},isEnclosed:function(){return this.textarea||n["a"].options.computed.isEnclosed.call(this)},noResizeHandle:function(){return this.noResize||this.autoGrow}},watch:{lazyValue:function(){!this.internalChange&&this.autoGrow&&this.$nextTick(this.calculateInputHeight)}},mounted:function(){var t=this;setTimeout(function(){t.autoGrow&&t.calculateInputHeight()},0),this.autoGrow&&this.noResize&&Object(s["b"])('"no-resize" is now implied when using "auto-grow", and can be removed',this)},methods:{calculateInputHeight:function(){var t=this.$refs.input;if(t){t.style.height=0;var e=t.scrollHeight,i=parseInt(this.rows,10)*parseFloat(this.rowHeight);t.style.height=Math.max(i,e)+"px"}},genInput:function(){var t=n["a"].options.methods.genInput.call(this);return t.tag="textarea",delete t.data.attrs.type,t.data.attrs.rows=this.rows,t},onInput:function(t){n["a"].options.methods.onInput.call(this,t),this.autoGrow&&this.calculateInputHeight()},onKeyDown:function(t){this.isFocused&&13===t.keyCode&&t.stopPropagation(),this.internalChange=!0,this.$emit("keydown",t)}}}},ac7c:function(t,e,i){"use strict";i("94a7");var n=i("9d26"),s=i("5368"),a=Object.assign||function(t){for(var e=1;e<arguments.length;e++){var i=arguments[e];for(var n in i)Object.prototype.hasOwnProperty.call(i,n)&&(t[n]=i[n])}return t};e["a"]={name:"v-checkbox",mixins:[s["a"]],props:{indeterminate:Boolean,indeterminateIcon:{type:String,default:"$vuetify.icons.checkboxIndeterminate"},onIcon:{type:String,default:"$vuetify.icons.checkboxOn"},offIcon:{type:String,default:"$vuetify.icons.checkboxOff"}},data:function(t){return{inputIndeterminate:t.indeterminate}},computed:{classes:function(){return{"v-input--selection-controls":!0,"v-input--checkbox":!0}},computedIcon:function(){return this.inputIndeterminate?this.indeterminateIcon:this.isActive?this.onIcon:this.offIcon}},watch:{indeterminate:function(t){this.inputIndeterminate=t}},methods:{genCheckbox:function(){return this.$createElement("div",{staticClass:"v-input--selection-controls__input"},[this.genInput("checkbox",a({},this.$attrs,{"aria-checked":this.inputIndeterminate?"mixed":this.isActive.toString()})),this.genRipple(this.setTextColor(this.computedColor)),this.$createElement(n["a"],this.setTextColor(this.computedColor,{props:{dark:this.dark,light:this.light}}),this.computedIcon)])},genDefaultSlot:function(){return[this.genCheckbox(),this.genLabel()]}}}},acd9:function(t,e,i){"use strict";var n=i("98d8"),s=i.n(n);s.a},b0af:function(t,e,i){"use strict";i("4c94"),i("d0e7");var n=i("b64a"),s=i("2b0e");function a(t,e,i){return e in t?Object.defineProperty(t,e,{value:i,enumerable:!0,configurable:!0,writable:!0}):t[e]=i,t}var o=s["a"].extend({name:"elevatable",props:{elevation:[Number,String]},computed:{computedElevation:function(){return this.elevation},elevationClasses:function(){return this.computedElevation?a({},"elevation-"+this.computedElevation,!0):{}}}}),r=i("23bf"),l=i("6a18"),c=i("58df"),u=Object.assign||function(t){for(var e=1;e<arguments.length;e++){var i=arguments[e];for(var n in i)Object.prototype.hasOwnProperty.call(i,n)&&(t[n]=i[n])}return t},h=Object(c["a"])(n["a"],o,r["a"],l["a"]).extend({name:"v-sheet",props:{tag:{type:String,default:"div"},tile:Boolean},computed:{classes:function(){return u({"v-sheet":!0,"v-sheet--tile":this.tile},this.themeClasses,this.elevationClasses)},styles:function(){return this.measurableStyles}},render:function(t){var e={class:this.classes,style:this.styles,on:this.$listeners};return t(this.tag,this.setBackgroundColor(this.color,e),this.$slots.default)}}),d=h,f=i("0d01"),p=Object.assign||function(t){for(var e=1;e<arguments.length;e++){var i=arguments[e];for(var n in i)Object.prototype.hasOwnProperty.call(i,n)&&(t[n]=i[n])}return t};e["a"]=Object(c["a"])(f["a"],d).extend({name:"v-card",props:{flat:Boolean,hover:Boolean,img:String,raised:Boolean},computed:{classes:function(){return p({"v-card":!0,"v-card--flat":this.flat,"v-card--hover":this.hover},d.options.computed.classes.call(this))},styles:function(){var t=p({},d.options.computed.styles.call(this));return this.img&&(t.background='url("'+this.img+'") center center / cover no-repeat'),t}},render:function(t){var e=this.generateRouteLink(this.classes),i=e.tag,n=e.data;return n.style=this.styles,t(i,this.setBackgroundColor(this.color,n),this.$slots.default)}})},b3df:function(t,e,i){},b56d:function(t,e,i){"use strict";var n=i("b974"),s=(i("8038"),i("c6a6")),a=s["a"],o=i("8654"),r=i("afdd"),l=i("d9bd"),c=a.extend({name:"v-overflow-btn",props:{segmented:Boolean,editable:Boolean,transition:n["a"].options.props.transition},computed:{classes:function(){return Object.assign(a.options.computed.classes.call(this),{"v-overflow-btn":!0,"v-overflow-btn--segmented":this.segmented,"v-overflow-btn--editable":this.editable})},isAnyValueAllowed:function(){return this.editable||a.options.computed.isAnyValueAllowed.call(this)},isSingle:function(){return!0},computedItems:function(){return this.segmented?this.allItems:this.filteredItems},$_menuProps:function(){var t=a.options.computed.$_menuProps.call(this);return t.transition=t.transition||"v-menu-transition",t}},methods:{genSelections:function(){return this.editable?a.options.methods.genSelections.call(this):n["a"].options.methods.genSelections.call(this)},genCommaSelection:function(t,e,i){return this.segmented?this.genSegmentedBtn(t):n["a"].options.methods.genCommaSelection.call(this,t,e,i)},genInput:function(){var t=o["a"].options.methods.genInput.call(this);return t.data.domProps.value=this.editable?this.internalSearch:"",t.data.attrs.readonly=!this.isAnyValueAllowed,t},genLabel:function(){if(this.editable&&this.isFocused)return null;var t=o["a"].options.methods.genLabel.call(this);return t?(t.data.style={},t):t},genSegmentedBtn:function(t){var e=this,i=this.getValue(t),n=this.computedItems.find(function(t){return e.getValue(t)===i})||t;return n.text&&n.callback?this.$createElement(r["a"],{props:{flat:!0},on:{click:function(t){t.stopPropagation(),n.callback(t)}}},[n.text]):(Object(l["c"])("When using 'segmented' prop without a selection slot, items must contain both a text and callback property",this),null)},setSelectedItems:function(){null==this.internalValue?this.selectedItems=[]:this.selectedItems=[this.internalValue]}}}),u=c,h=(i("b3df"),i("80d2")),d={name:"v-combobox",extends:s["a"],props:{delimiters:{type:Array,default:function(){return[]}},returnObject:{type:Boolean,default:!0}},data:function(){return{editingIndex:-1}},computed:{counterValue:function(){return this.multiple?this.selectedItems.length:(this.internalSearch||"").toString().length},hasSlot:function(){return n["a"].options.computed.hasSlot.call(this)||this.multiple},isAnyValueAllowed:function(){return!0},menuCanShow:function(){return!!this.isFocused&&(this.hasDisplayedItems||!!this.$slots["no-data"]&&!this.hideNoData)}},methods:{onFilteredItemsChanged:function(){},onInternalSearchChanged:function(t){if(t&&this.multiple&&this.delimiters.length){var e=this.delimiters.find(function(e){return t.endsWith(e)});null!=e&&(this.internalSearch=t.slice(0,t.length-e.length),this.updateTags())}this.updateMenuDimensions()},genChipSelection:function(t,e){var i=this,s=n["a"].options.methods.genChipSelection.call(this,t,e);return this.multiple&&(s.componentOptions.listeners.dblclick=function(){i.editingIndex=e,i.internalSearch=i.getText(t),i.selectedIndex=-1}),s},onChipInput:function(t){n["a"].options.methods.onChipInput.call(this,t),this.editingIndex=-1},onEnterDown:function(t){t.preventDefault(),n["a"].options.methods.onEnterDown.call(this),this.getMenuIndex()>-1||this.updateSelf()},onKeyDown:function(t){var e=t.keyCode;n["a"].options.methods.onKeyDown.call(this,t),this.multiple&&e===h["p"].left&&0===this.$refs.input.selectionStart&&this.updateSelf(),this.changeSelectedIndex(e)},onTabDown:function(t){if(this.multiple&&this.internalSearch&&-1===this.getMenuIndex())return t.preventDefault(),t.stopPropagation(),this.updateTags();s["a"].options.methods.onTabDown.call(this,t)},selectItem:function(t){this.editingIndex>-1?this.updateEditing():n["a"].options.methods.selectItem.call(this,t)},setSelectedItems:function(){null==this.internalValue||""===this.internalValue?this.selectedItems=[]:this.selectedItems=this.multiple?this.internalValue:[this.internalValue]},setValue:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:this.internalSearch;n["a"].options.methods.setValue.call(this,t)},updateEditing:function(){var t=this.internalValue.slice();t[this.editingIndex]=this.internalSearch,this.setValue(t),this.editingIndex=-1},updateCombobox:function(){var t=Boolean(this.$scopedSlots.selection)||this.hasChips;t&&!this.searchIsDirty||(this.internalSearch!==this.getText(this.internalValue)&&this.setValue(),t&&(this.internalSearch=void 0))},updateSelf:function(){this.multiple?this.updateTags():this.updateCombobox()},updateTags:function(){var t=this.getMenuIndex();if(!(t<0)||this.searchIsDirty){if(this.editingIndex>-1)return this.updateEditing();var e=this.selectedItems.indexOf(this.internalSearch);if(e>-1){var i=this.internalValue.slice();i.splice(e,1),this.setValue(i)}if(t>-1)return this.internalSearch=null;this.selectItem(this.internalSearch),this.internalSearch=null}}}},f=d,p=i("7cf7"),m=i("ab6d");i.d(e,"a",function(){return v});var v={functional:!0,$_wrapperFor:n["a"],props:{autocomplete:Boolean,combobox:Boolean,multiple:Boolean,tags:Boolean,editable:Boolean,overflow:Boolean,segmented:Boolean},render:function(t,e){var i=e.props,s=e.data,o=e.slots,r=e.parent;Object(m["a"])(s);var c=Object(p["a"])(o(),t);return i.autocomplete&&Object(l["d"])("<v-select autocomplete>","<v-autocomplete>",v,r),i.combobox&&Object(l["d"])("<v-select combobox>","<v-combobox>",v,r),i.tags&&Object(l["d"])("<v-select tags>","<v-combobox multiple>",v,r),i.overflow&&Object(l["d"])("<v-select overflow>","<v-overflow-btn>",v,r),i.segmented&&Object(l["d"])("<v-select segmented>","<v-overflow-btn segmented>",v,r),i.editable&&Object(l["d"])("<v-select editable>","<v-overflow-btn editable>",v,r),s.attrs=s.attrs||{},i.combobox||i.tags?(s.attrs.multiple=i.tags,t(f,s,c)):i.autocomplete?(s.attrs.multiple=i.multiple,t(a,s,c)):i.overflow||i.segmented||i.editable?(s.attrs.segmented=i.segmented,s.attrs.editable=i.editable,t(u,s,c)):(s.attrs.multiple=i.multiple,t(n["a"],s,c))}};e["b"]=v},b847:function(t,e,i){},b974:function(t,e,i){"use strict";i("da37"),i("b847"),i("bf5a");var n=i("58df"),s=i("9d26"),a=i("b64a"),o=i("6a18"),r=i("98a1"),l=Object.assign||function(t){for(var e=1;e<arguments.length;e++){var i=arguments[e];for(var n in i)Object.prototype.hasOwnProperty.call(i,n)&&(t[n]=i[n])}return t},c=Object(n["a"])(a["a"],o["a"],r["a"]).extend({name:"v-chip",props:{close:Boolean,disabled:Boolean,label:Boolean,outline:Boolean,selected:Boolean,small:Boolean,textColor:String,value:{type:Boolean,default:!0}},computed:{classes:function(){return l({"v-chip--disabled":this.disabled,"v-chip--selected":this.selected&&!this.disabled,"v-chip--label":this.label,"v-chip--outline":this.outline,"v-chip--small":this.small,"v-chip--removable":this.close},this.themeClasses)}},methods:{genClose:function(t){var e=this,i={staticClass:"v-chip__close",on:{click:function(t){t.stopPropagation(),e.$emit("input",!1)}}};return t("div",i,[t(s["a"],"$vuetify.icons.delete")])},genContent:function(t){return t("span",{staticClass:"v-chip__content"},[this.$slots.default,this.close&&this.genClose(t)])}},render:function(t){var e=this.setBackgroundColor(this.color,{staticClass:"v-chip",class:this.classes,attrs:{tabindex:this.disabled?-1:0},directives:[{name:"show",value:this.isActive}],on:this.$listeners}),i=this.textColor||this.outline&&this.color;return t("span",this.setTextColor(i,e),[this.genContent(t)])}}),u=c,h=i("e449"),d=h["a"],f=(i("4c94"),i("41f4")),p=i("ce7e"),m=p["a"],v=i("e0c7"),g=v["a"],b=i("ba95"),I=i("40fe"),x=i("5d23"),y=i("8860"),S=i("80d2"),C={name:"v-select-list",mixins:[a["a"],o["a"]],props:{action:Boolean,dense:Boolean,hideSelected:Boolean,items:{type:Array,default:function(){return[]}},itemAvatar:{type:[String,Array,Function],default:"avatar"},itemDisabled:{type:[String,Array,Function],default:"disabled"},itemText:{type:[String,Array,Function],default:"text"},itemValue:{type:[String,Array,Function],default:"value"},noDataText:String,noFilter:Boolean,searchInput:{default:null},selectedItems:{type:Array,default:function(){return[]}}},computed:{parsedItems:function(){var t=this;return this.selectedItems.map(function(e){return t.getValue(e)})},tileActiveClass:function(){return Object.keys(this.setTextColor(this.color).class||{}).join(" ")},staticNoDataTile:function(){var t={on:{mousedown:function(t){return t.preventDefault()}}};return this.$createElement(b["a"],t,[this.genTileContent(this.noDataText)])}},methods:{genAction:function(t,e){var i=this,n={on:{click:function(e){e.stopPropagation(),i.$emit("select",t)}}};return this.$createElement(I["a"],n,[this.$createElement(f["a"],{props:{color:this.color,inputValue:e}})])},genDivider:function(t){return this.$createElement(m,{props:t})},genFilteredText:function(t){if(t=(t||"").toString(),!this.searchInput||this.noFilter)return Object(S["h"])(t);var e=this.getMaskedCharacters(t),i=e.start,n=e.middle,s=e.end;return""+Object(S["h"])(i)+this.genHighlight(n)+Object(S["h"])(s)},genHeader:function(t){return this.$createElement(g,{props:t},t.header)},genHighlight:function(t){return'<span class="v-list__tile__mask">'+Object(S["h"])(t)+"</span>"},getMaskedCharacters:function(t){var e=(this.searchInput||"").toString().toLocaleLowerCase(),i=t.toLocaleLowerCase().indexOf(e);if(i<0)return{start:"",middle:t,end:""};var n=t.slice(0,i),s=t.slice(i,i+e.length),a=t.slice(i+e.length);return{start:n,middle:s,end:a}},genTile:function(t){var e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:null,i=this,n=arguments.length>2&&void 0!==arguments[2]&&arguments[2],s=arguments.length>3&&void 0!==arguments[3]?arguments[3]:this.hasItem(t);t===Object(t)&&(n=this.getAvatar(t),e=null!==e?e:this.getDisabled(t));var a={on:{mousedown:function(t){t.preventDefault()},click:function(){return e||i.$emit("select",t)}},props:{activeClass:this.tileActiveClass,avatar:n,disabled:e,ripple:!0,value:s}};if(!this.$scopedSlots.item)return this.$createElement(b["a"],a,[this.action&&!this.hideSelected&&this.items.length>0?this.genAction(t,s):null,this.genTileContent(t)]);var o=this,r=this.$scopedSlots.item({parent:o,item:t,tile:a});return this.needsTile(r)?this.$createElement(b["a"],a,r):r},genTileContent:function(t){var e=this.genFilteredText(this.getText(t));return this.$createElement(x["a"],[this.$createElement(x["b"],{domProps:{innerHTML:e}})])},hasItem:function(t){return this.parsedItems.indexOf(this.getValue(t))>-1},needsTile:function(t){return 1!==t.length||null==t[0].componentOptions||"v-list-tile"!==t[0].componentOptions.Ctor.options.name},getAvatar:function(t){return Boolean(Object(S["k"])(t,this.itemAvatar,!1))},getDisabled:function(t){return Boolean(Object(S["k"])(t,this.itemDisabled,!1))},getText:function(t){return String(Object(S["k"])(t,this.itemText,t))},getValue:function(t){return Object(S["k"])(t,this.itemValue,this.getText(t))}},render:function(){var t=[],e=!0,i=!1,n=void 0;try{for(var s,a=this.items[Symbol.iterator]();!(e=(s=a.next()).done);e=!0){var o=s.value;this.hideSelected&&this.hasItem(o)||(null==o?t.push(this.genTile(o)):o.header?t.push(this.genHeader(o)):o.divider?t.push(this.genDivider(o)):t.push(this.genTile(o)))}}catch(r){i=!0,n=r}finally{try{!e&&a.return&&a.return()}finally{if(i)throw n}}return t.length||t.push(this.$slots["no-data"]||this.staticNoDataTile),this.$slots["prepend-item"]&&t.unshift(this.$slots["prepend-item"]),this.$slots["append-item"]&&t.push(this.$slots["append-item"]),this.$createElement("div",{staticClass:"v-select-list v-card",class:this.themeClasses},[this.$createElement(y["a"],{props:{dense:this.dense}},t)])}},$=i("8654"),w=i("5e28"),k=i("60e6"),O=i("c584"),A=i("d9bd");i.d(e,"b",function(){return T});var D=Object.assign||function(t){for(var e=1;e<arguments.length;e++){var i=arguments[e];for(var n in i)Object.prototype.hasOwnProperty.call(i,n)&&(t[n]=i[n])}return t};function V(t,e,i){return e in t?Object.defineProperty(t,e,{value:i,enumerable:!0,configurable:!0,writable:!0}):t[e]=i,t}var T={closeOnClick:!1,closeOnContentClick:!1,openOnClick:!1,maxHeight:300};e["a"]=$["a"].extend({name:"v-select",directives:{ClickOutside:O["a"]},mixins:[w["a"],k["a"]],props:{appendIcon:{type:String,default:"$vuetify.icons.dropdown"},appendIconCb:Function,attach:{type:null,default:!1},browserAutocomplete:{type:String,default:"on"},cacheItems:Boolean,chips:Boolean,clearable:Boolean,deletableChips:Boolean,dense:Boolean,hideSelected:Boolean,items:{type:Array,default:function(){return[]}},itemAvatar:{type:[String,Array,Function],default:"avatar"},itemDisabled:{type:[String,Array,Function],default:"disabled"},itemText:{type:[String,Array,Function],default:"text"},itemValue:{type:[String,Array,Function],default:"value"},menuProps:{type:[String,Array,Object],default:function(){return T}},multiple:Boolean,openOnClear:Boolean,returnObject:Boolean,searchInput:{default:null},smallChips:Boolean},data:function(t){return{attrsInput:{role:"combobox"},cachedItems:t.cacheItems?t.items:[],content:null,isBooted:!1,isMenuActive:!1,lastItem:20,lazyValue:void 0!==t.value?t.value:t.multiple?[]:void 0,selectedIndex:-1,selectedItems:[],keyboardLookupPrefix:"",keyboardLookupLastTime:0}},computed:{allItems:function(){return this.filterDuplicates(this.cachedItems.concat(this.items))},classes:function(){return Object.assign({},$["a"].options.computed.classes.call(this),{"v-select":!0,"v-select--chips":this.hasChips,"v-select--chips--small":this.smallChips,"v-select--is-menu-active":this.isMenuActive})},computedItems:function(){return this.allItems},counterValue:function(){return this.multiple?this.selectedItems.length:(this.getText(this.selectedItems[0])||"").toString().length},directives:function(){return this.isFocused?[{name:"click-outside",value:this.blur,args:{closeConditional:this.closeConditional}}]:void 0},dynamicHeight:function(){return"auto"},hasChips:function(){return this.chips||this.smallChips},hasSlot:function(){return Boolean(this.hasChips||this.$scopedSlots.selection)},isDirty:function(){return this.selectedItems.length>0},listData:function(){var t=this.$vnode&&this.$vnode.context.$options._scopeId;return{attrs:t?V({},t,!0):null,props:{action:this.multiple&&!this.isHidingSelected,color:this.color,dense:this.dense,hideSelected:this.hideSelected,items:this.virtualizedItems,noDataText:this.$vuetify.t(this.noDataText),selectedItems:this.selectedItems,itemAvatar:this.itemAvatar,itemDisabled:this.itemDisabled,itemValue:this.itemValue,itemText:this.itemText},on:{select:this.selectItem},scopedSlots:{item:this.$scopedSlots.item}}},staticList:function(){return(this.$slots["no-data"]||this.$slots["prepend-item"]||this.$slots["append-item"])&&Object(A["a"])("assert: staticList should not be called if slots are used"),this.$createElement(C,this.listData)},virtualizedItems:function(){return this.$_menuProps.auto?this.computedItems:this.computedItems.slice(0,this.lastItem)},menuCanShow:function(){return!0},$_menuProps:function(){var t=void 0;return t="string"===typeof this.menuProps?this.menuProps.split(","):this.menuProps,Array.isArray(t)&&(t=t.reduce(function(t,e){return t[e.trim()]=!0,t},{})),D({},T,{value:this.menuCanShow&&this.isMenuActive,nudgeBottom:this.nudgeBottom?this.nudgeBottom:t.offsetY?1:0},t)}},watch:{internalValue:function(t){this.initialValue=t,this.setSelectedItems()},isBooted:function(){var t=this;this.$nextTick(function(){t.content&&t.content.addEventListener&&t.content.addEventListener("scroll",t.onScroll,!1)})},isMenuActive:function(t){t&&(this.isBooted=!0)},items:{immediate:!0,handler:function(t){this.cacheItems&&(this.cachedItems=this.filterDuplicates(this.cachedItems.concat(t))),this.setSelectedItems()}}},mounted:function(){this.content=this.$refs.menu&&this.$refs.menu.$refs.content},methods:{blur:function(t){this.isMenuActive=!1,this.isFocused=!1,this.$refs.input&&this.$refs.input.blur(),this.selectedIndex=-1,this.onBlur(t)},activateMenu:function(){this.isMenuActive=!0},clearableCallback:function(){var t=this;this.setValue(this.multiple?[]:void 0),this.$nextTick(function(){return t.$refs.input.focus()}),this.openOnClear&&(this.isMenuActive=!0)},closeConditional:function(t){return!!this.content&&!this.content.contains(t.target)&&!!this.$el&&!this.$el.contains(t.target)&&t.target!==this.$el},filterDuplicates:function(t){for(var e=new Map,i=0;i<t.length;++i){var n=t[i],s=this.getValue(n);!e.has(s)&&e.set(s,n)}return Array.from(e.values())},findExistingIndex:function(t){var e=this,i=this.getValue(t);return(this.internalValue||[]).findIndex(function(t){return e.valueComparator(e.getValue(t),i)})},genChipSelection:function(t,e){var i=this,n=this.disabled||this.readonly||this.getDisabled(t);return this.$createElement(u,{staticClass:"v-chip--select-multi",attrs:{tabindex:-1},props:{close:this.deletableChips&&!n,disabled:n,selected:e===this.selectedIndex,small:this.smallChips},on:{click:function(t){n||(t.stopPropagation(),i.selectedIndex=e)},input:function(){return i.onChipInput(t)}},key:this.getValue(t)},this.getText(t))},genCommaSelection:function(t,e,i){var n=JSON.stringify(this.getValue(t)),s=e===this.selectedIndex&&this.color,a=this.disabled||this.getDisabled(t);return this.$createElement("div",this.setTextColor(s,{staticClass:"v-select__selection v-select__selection--comma",class:{"v-select__selection--disabled":a},key:n}),this.getText(t)+(i?"":", "))},genDefaultSlot:function(){var t=this.genSelections(),e=this.genInput();return Array.isArray(t)?t.push(e):(t.children=t.children||[],t.children.push(e)),[this.$createElement("div",{staticClass:"v-select__slot",directives:this.directives},[this.genLabel(),this.prefix?this.genAffix("prefix"):null,t,this.suffix?this.genAffix("suffix"):null,this.genClearIcon(),this.genIconSlot()]),this.genMenu(),this.genProgress()]},genInput:function(){var t=$["a"].options.methods.genInput.call(this);return t.data.domProps.value=null,t.data.attrs.readonly=!0,t.data.attrs["aria-readonly"]=String(this.readonly),t.data.on.keypress=this.onKeyPress,t},genList:function(){return this.$slots["no-data"]||this.$slots["prepend-item"]||this.$slots["append-item"]?this.genListWithSlot():this.staticList},genListWithSlot:function(){var t=this,e=["prepend-item","no-data","append-item"].filter(function(e){return t.$slots[e]}).map(function(e){return t.$createElement("template",{slot:e},t.$slots[e])});return this.$createElement(C,D({},this.listData),e)},genMenu:function(){var t=this,e=this.$_menuProps;e.activator=this.$refs["input-slot"];var i=Object.keys(d.options.props),n=Object.keys(this.$attrs).reduce(function(t,e){return i.includes(Object(S["b"])(e))&&t.push(e),t},[]),s=!0,a=!1,o=void 0;try{for(var r,l=n[Symbol.iterator]();!(s=(r=l.next()).done);s=!0){var c=r.value;e[Object(S["b"])(c)]=this.$attrs[c]}}catch(u){a=!0,o=u}finally{try{!s&&l.return&&l.return()}finally{if(a)throw o}}return""===this.attach||!0===this.attach||"attach"===this.attach?e.attach=this.$el:e.attach=this.attach,this.$createElement(d,{props:e,on:{input:function(e){t.isMenuActive=e,t.isFocused=e}},ref:"menu"},[this.genList()])},genSelections:function(){var t=this.selectedItems.length,e=new Array(t),i=void 0;i=this.$scopedSlots.selection?this.genSlotSelection:this.hasChips?this.genChipSelection:this.genCommaSelection;while(t--)e[t]=i(this.selectedItems[t],t,t===e.length-1);return this.$createElement("div",{staticClass:"v-select__selections"},e)},genSlotSelection:function(t,e){return this.$scopedSlots.selection({parent:this,item:t,index:e,selected:e===this.selectedIndex,disabled:this.disabled||this.readonly})},getMenuIndex:function(){return this.$refs.menu?this.$refs.menu.listIndex:-1},getDisabled:function(t){return Object(S["k"])(t,this.itemDisabled,!1)},getText:function(t){return Object(S["k"])(t,this.itemText,t)},getValue:function(t){return Object(S["k"])(t,this.itemValue,this.getText(t))},onBlur:function(t){this.$emit("blur",t)},onChipInput:function(t){this.multiple?this.selectItem(t):this.setValue(null),0===this.selectedItems.length&&(this.isMenuActive=!0),this.selectedIndex=-1},onClick:function(){this.isDisabled||(this.isMenuActive=!0,this.isFocused||(this.isFocused=!0,this.$emit("focus")))},onEnterDown:function(){this.onBlur()},onEscDown:function(t){t.preventDefault(),this.isMenuActive&&(t.stopPropagation(),this.isMenuActive=!1)},onKeyPress:function(t){var e=this;if(!this.multiple){var i=1e3,n=performance.now();n-this.keyboardLookupLastTime>i&&(this.keyboardLookupPrefix=""),this.keyboardLookupPrefix+=t.key.toLowerCase(),this.keyboardLookupLastTime=n;var s=this.allItems.find(function(t){return e.getText(t).toLowerCase().startsWith(e.keyboardLookupPrefix)});void 0!==s&&this.setValue(this.returnObject?s:this.getValue(s))}},onKeyDown:function(t){var e=t.keyCode;return this.readonly||this.isMenuActive||![S["p"].enter,S["p"].space,S["p"].up,S["p"].down].includes(e)||this.activateMenu(),this.isMenuActive&&this.$refs.menu&&this.$refs.menu.changeListIndex(t),e===S["p"].enter?this.onEnterDown(t):e===S["p"].esc?this.onEscDown(t):e===S["p"].tab?this.onTabDown(t):void 0},onMouseUp:function(t){var e=this;if(this.hasMouseDown){var i=this.$refs["append-inner"];this.isMenuActive&&i&&(i===t.target||i.contains(t.target))?this.$nextTick(function(){return e.isMenuActive=!e.isMenuActive}):this.isEnclosed&&!this.isDisabled&&(this.isMenuActive=!0)}$["a"].options.methods.onMouseUp.call(this,t)},onScroll:function(){var t=this;if(this.isMenuActive){if(this.lastItem>=this.computedItems.length)return;var e=this.content.scrollHeight-(this.content.scrollTop+this.content.clientHeight)<200;e&&(this.lastItem+=20)}else requestAnimationFrame(function(){return t.content.scrollTop=0})},onTabDown:function(t){var e=this.getMenuIndex(),i=this.$refs.menu.tiles[e];i&&i.className.indexOf("v-list__tile--highlighted")>-1&&this.isMenuActive&&e>-1?(t.preventDefault(),t.stopPropagation(),i.click()):this.blur(t)},selectItem:function(t){var e=this;if(this.multiple){var i=(this.internalValue||[]).slice(),n=this.findExistingIndex(t);-1!==n?i.splice(n,1):i.push(t),this.setValue(i.map(function(t){return e.returnObject?t:e.getValue(t)})),this.$nextTick(function(){e.$refs.menu&&e.$refs.menu.updateDimensions()})}else this.setValue(this.returnObject?t:this.getValue(t)),this.isMenuActive=!1},setMenuIndex:function(t){this.$refs.menu&&(this.$refs.menu.listIndex=t)},setSelectedItems:function(){var t=this,e=[],i=this.multiple&&Array.isArray(this.internalValue)?this.internalValue:[this.internalValue],n=function(i){var n=t.allItems.findIndex(function(e){return t.valueComparator(t.getValue(e),t.getValue(i))});n>-1&&e.push(t.allItems[n])},s=!0,a=!1,o=void 0;try{for(var r,l=i[Symbol.iterator]();!(s=(r=l.next()).done);s=!0){var c=r.value;n(c)}}catch(u){a=!0,o=u}finally{try{!s&&l.return&&l.return()}finally{if(a)throw o}}this.selectedItems=e},setValue:function(t){var e=this.internalValue;this.internalValue=t,t!==e&&this.$emit("change",t)}}})},bf5a:function(t,e,i){},c6a6:function(t,e,i){"use strict";i("b3df");var n=i("b974"),s=i("8654"),a=i("80d2"),o=Object.assign||function(t){for(var e=1;e<arguments.length;e++){var i=arguments[e];for(var n in i)Object.prototype.hasOwnProperty.call(i,n)&&(t[n]=i[n])}return t},r=o({},n["b"],{offsetY:!0,offsetOverflow:!0,transition:!1});e["a"]=n["a"].extend({name:"v-autocomplete",props:{allowOverflow:{type:Boolean,default:!0},browserAutocomplete:{type:String,default:"off"},filter:{type:Function,default:function(t,e,i){return i.toLocaleLowerCase().indexOf(e.toLocaleLowerCase())>-1}},hideNoData:Boolean,noFilter:Boolean,searchInput:{default:void 0},menuProps:{type:n["a"].options.props.menuProps.type,default:function(){return r}},autoSelectFirst:{type:Boolean,default:!1}},data:function(t){return{attrsInput:null,lazySearch:t.searchInput}},computed:{classes:function(){return Object.assign({},n["a"].options.computed.classes.call(this),{"v-autocomplete":!0,"v-autocomplete--is-selecting-index":this.selectedIndex>-1})},computedItems:function(){return this.filteredItems},selectedValues:function(){var t=this;return this.selectedItems.map(function(e){return t.getValue(e)})},hasDisplayedItems:function(){var t=this;return this.hideSelected?this.filteredItems.some(function(e){return!t.hasItem(e)}):this.filteredItems.length>0},currentRange:function(){return null==this.selectedItem?0:this.getText(this.selectedItem).toString().length},filteredItems:function(){var t=this;return!this.isSearching||this.noFilter||null==this.internalSearch?this.allItems:this.allItems.filter(function(e){return t.filter(e,t.internalSearch.toString(),t.getText(e).toString())})},internalSearch:{get:function(){return this.lazySearch},set:function(t){this.lazySearch=t,this.$emit("update:searchInput",t)}},isAnyValueAllowed:function(){return!1},isDirty:function(){return this.searchIsDirty||this.selectedItems.length>0},isSearching:function(){return this.multiple?this.searchIsDirty:this.searchIsDirty&&this.internalSearch!==this.getText(this.selectedItem)},menuCanShow:function(){return!!this.isFocused&&(this.hasDisplayedItems||!this.hideNoData)},$_menuProps:function(){var t=n["a"].options.computed.$_menuProps.call(this);return t.contentClass=("v-autocomplete__content "+(t.contentClass||"")).trim(),o({},r,t)},searchIsDirty:function(){return null!=this.internalSearch&&""!==this.internalSearch},selectedItem:function(){var t=this;return this.multiple?null:this.selectedItems.find(function(e){return t.valueComparator(t.getValue(e),t.getValue(t.internalValue))})},listData:function(){var t=n["a"].options.computed.listData.call(this);return Object.assign(t.props,{items:this.virtualizedItems,noFilter:this.noFilter||!this.isSearching||!this.filteredItems.length,searchInput:this.internalSearch}),t}},watch:{filteredItems:function(t){this.onFilteredItemsChanged(t)},internalValue:function(){this.setSearch()},isFocused:function(t){t?this.$refs.input&&this.$refs.input.select():this.updateSelf()},isMenuActive:function(t){!t&&this.hasSlot&&(this.lazySearch=null)},items:function(t,e){e&&e.length||!this.hideNoData||!this.isFocused||this.isMenuActive||!t.length||this.activateMenu()},searchInput:function(t){this.lazySearch=t},internalSearch:function(t){this.onInternalSearchChanged(t)},itemText:function(){this.updateSelf()}},created:function(){this.setSearch()},methods:{onFilteredItemsChanged:function(t){var e=this;this.setMenuIndex(-1),this.$nextTick(function(){e.setMenuIndex(t.length>0&&(1===t.length||e.autoSelectFirst)?0:-1)})},onInternalSearchChanged:function(t){this.updateMenuDimensions()},updateMenuDimensions:function(){this.isMenuActive&&this.$refs.menu&&this.$refs.menu.updateDimensions()},changeSelectedIndex:function(t){if(!this.searchIsDirty&&[a["p"].backspace,a["p"].left,a["p"].right,a["p"].delete].includes(t)){var e=this.selectedItems.length-1;if(t===a["p"].left)this.selectedIndex=-1===this.selectedIndex?e:this.selectedIndex-1;else if(t===a["p"].right)this.selectedIndex=this.selectedIndex>=e?-1:this.selectedIndex+1;else if(-1===this.selectedIndex)return void(this.selectedIndex=e);var i=this.selectedItems[this.selectedIndex];if([a["p"].backspace,a["p"].delete].includes(t)&&!this.getDisabled(i)){var n=this.selectedIndex===e?this.selectedIndex-1:this.selectedItems[this.selectedIndex+1]?this.selectedIndex:-1;-1===n?this.setValue(this.multiple?[]:void 0):this.selectItem(i),this.selectedIndex=n}}},clearableCallback:function(){this.internalSearch=void 0,n["a"].options.methods.clearableCallback.call(this)},genInput:function(){var t=s["a"].options.methods.genInput.call(this);return t.data.attrs.role="combobox",t.data.domProps.value=this.internalSearch,t},genSelections:function(){return this.hasSlot||this.multiple?n["a"].options.methods.genSelections.call(this):[]},onClick:function(){this.isDisabled||(this.selectedIndex>-1?this.selectedIndex=-1:this.onFocus(),this.activateMenu())},onEnterDown:function(){},onInput:function(t){this.selectedIndex>-1||(t.target.value&&(this.activateMenu(),this.isAnyValueAllowed||this.setMenuIndex(0)),this.mask&&this.resetSelections(t.target),this.internalSearch=t.target.value,this.badInput=t.target.validity&&t.target.validity.badInput)},onKeyDown:function(t){var e=t.keyCode;n["a"].options.methods.onKeyDown.call(this,t),this.changeSelectedIndex(e)},onTabDown:function(t){n["a"].options.methods.onTabDown.call(this,t),this.updateSelf()},setSelectedItems:function(){n["a"].options.methods.setSelectedItems.call(this),this.isFocused||this.setSearch()},setSearch:function(){var t=this;this.$nextTick(function(){t.internalSearch=t.multiple&&t.internalSearch&&t.isMenuActive?t.internalSearch:!t.selectedItems.length||t.multiple||t.hasSlot?null:t.getText(t.selectedItem)})},updateSelf:function(){this.updateAutocomplete()},updateAutocomplete:function(){(this.searchIsDirty||this.internalValue)&&(this.valueComparator(this.internalSearch,this.getValue(this.internalValue))||this.setSearch())},hasItem:function(t){return this.selectedValues.indexOf(this.getValue(t))>-1}}})},d0e7:function(t,e,i){},e0c7:function(t,e,i){"use strict";i("90bd");var n=i("6a18"),s=i("58df"),a=Object.assign||function(t){for(var e=1;e<arguments.length;e++){var i=arguments[e];for(var n in i)Object.prototype.hasOwnProperty.call(i,n)&&(t[n]=i[n])}return t};e["a"]=Object(s["a"])(n["a"]).extend({name:"v-subheader",props:{inset:Boolean},render:function(t){return t("div",{staticClass:"v-subheader",class:a({"v-subheader--inset":this.inset},this.themeClasses),attrs:this.$attrs,on:this.$listeners},this.$slots.default)}})}}]);
//# sourceMappingURL=chunk-19bd4fc8.5e548ef2.js.map