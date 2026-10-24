import app from 'flarum/admin/app';
import ExtensionPage from 'flarum/admin/components/ExtensionPage';
import Form from 'flarum/common/components/Form';
import Button from 'flarum/common/components/Button';
import Switch from 'flarum/common/components/Switch';
import defaults from '../../../resources/defaults.json';
const t = key => app.translator.trans('edgeinfinity-dice.admin.'+key);
const key='edgeinfinity-dice.config';
export default class DiceSettingsPage extends ExtensionPage {
  oninit(vnode) {
    super.oninit(vnode);
    try { this.config=JSON.parse(this.setting(key)() || JSON.stringify(defaults)); }
    catch {this.config=JSON.parse(JSON.stringify(defaults));}
    this.specialText=this.config.specialNumbers.join(', ');
    this.priorityText=this.config.priority.join(', ');
  }
  change() {this.setting(key)(JSON.stringify(this.config));}
  field(name,input,help) {return m('label.EdgeDiceAdmin-field',[m('span',t(name)),input,help&&m('span.helpText',t(help))]);}
  number(name,min,max) {return this.field(name,m('input.FormControl',{type:'number',required:true,min,max,step:1,value:this.config[name],oninput:e=>{this.config[name]=Number(e.target.value);this.change();}}));}
  toggle(name) {return m('.Form-group',m(Switch,{state:this.config[name],onchange:value=>{this.config[name]=value;this.change();}},t(name)));}
  content() {
    return m('.ExtensionPage-settings.EdgeDiceAdmin',m('.container',[
      m('p.helpText',t('intro')),m('p.Alert',t('lifecycle')),
      m(Form,{onsubmit:this.saveSettings.bind(this)},[
        m('h3',t('rules')),
        this.number('maxDice',1,100),
        this.toggle('criticalEnabled'),this.number('criticalMinFaces',2,2000000001),this.number('criticalPercent',1,50),this.toggle('successHigh'),
        m('p.helpText',t('critical_help')),
        this.toggle('signEnabled'),this.toggle('repeatEnabled'),this.toggle('sequenceEnabled'),this.number('digitsMin',2,10),this.toggle('specialEnabled'),
        this.field('specialNumbers',m('input.FormControl',{value:this.specialText,oninput:e=>{this.specialText=e.target.value;this.config.specialNumbers=this.specialText.trim()?this.specialText.split(/[,，\s]+/).filter(Boolean).map(Number):[];this.change();}}),'numbers_help'),
        this.field('priority',m('input.FormControl',{value:this.priorityText,oninput:e=>{this.priorityText=e.target.value;this.config.priority=this.priorityText.split(/[,，\s]+/).filter(Boolean);this.change();}}),'priority_help'),
        m('h3',t('colors')),m('p.helpText',t('colors_help')),
        m('.EdgeDiceAdmin-colors',Object.keys(defaults.colors).map(effect=>m('fieldset',[
          m('legend',t('effect_'+effect)),
          ...['light','dark'].map(theme=>m('.EdgeDiceAdmin-theme',[
            m('h4',t(theme)),...['text','background','icon'].map(property=>{
              const value=this.config.colors[effect][theme][property];
              return m('label',[m('span',t(property)),m('input.FormControl',{type:'text',maxlength:9,placeholder:defaults.colors[effect][theme][property]||t('inherit'),value,pattern:'(#([a-fA-F0-9]{3}|[a-fA-F0-9]{4}|[a-fA-F0-9]{6}|[a-fA-F0-9]{8}))?',oninput:e=>{this.config.colors[effect][theme][property]=e.target.value;this.change();}})]);
            }),
            m('span.EdgeDiceAdmin-swatch',{style:{color:this.config.colors[effect][theme].text||undefined,background:this.config.colors[effect][theme].background||undefined}},'20 [1, 20] ⚄'),
          ])),
        ]))),
        m('.Form-group.Form-controls',[this.submitButton(),m(Button,{className:'Button',onclick:()=>{this.config=JSON.parse(JSON.stringify(defaults));this.specialText=this.config.specialNumbers.join(', ');this.priorityText=this.config.priority.join(', ');this.change();}},t('reset'))]),
      ]),
    ]));
  }
}
