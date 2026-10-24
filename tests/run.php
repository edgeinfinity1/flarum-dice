<?php
require __DIR__.'/bootstrap.php';
use EdgeInfinity\Dice\{DiceSyntax,DiceConfig,ConfigureFormatter,RenderDice,RollPolicy};
use Flarum\Post\CommentPost;
use Flarum\Settings\SettingsRepositoryInterface;
$count=0;
function check(bool $condition,string $label):void {global $count;$count++;if(!$condition)throw new RuntimeException($label);}
function rejects(callable $f,string $label):void {try{$f();}catch(InvalidArgumentException|Flarum\Foundation\ValidationException $e){check(true,$label);return;}check(false,$label);}
$c=new s9e\TextFormatter\Configurator;
$c->rendering->setEngine('PHP');$c->Litedown;$c->Escaper;$c->BBCodes->addFromRepository('QUOTE');
(new ConfigureFormatter)($c);$f=$c->finalize();
$parse=fn(string $text):string=>$f['parser']->parse($text);
$cases=['[1, 6]'=>[[1,6]],'[ -5 , +10 ]'=>[[-5,10]],'[0,0]'=>[[0,0]],'[10,5]'=>[], '[1.2,6]'=>[],'[1e2,200]'=>[], '[1,1000000001]'=>[], '[1, 6, 8]'=>[], '[1,6](https://example.com)'=>[], '\\[1,6]'=>[], '> [1,6]'=>[], '[quote][1,6][/quote]'=>[], '中文 [1,6] + [-10,-1]'=>[[1,6],[-10,-1]]];
$cases[chr(96).'[1,6]'.chr(96)]=[];$cases[str_repeat(chr(96),3)."\n[1,6]\n".str_repeat(chr(96),3)]=[];
foreach($cases as $s=>$ranges){$xml=$parse($s);check(DiceSyntax::ranges($xml)===$ranges,'parse '.$s);check(s9e\TextFormatter\Unparser::unparse($xml)===$s,'unparse '.$s);}
$settings=new class implements SettingsRepositoryInterface {
 public array $values=[];public function all():array{return $this->values;}public function get(string $key,mixed $default=null):mixed{return $this->values[$key]??$default;}public function set(string $key,mixed $value):void{$this->values[$key]=$value;}public function delete(string $keyLike):void{unset($this->values[$keyLike]);}
};
$translator=new Flarum\Locale\Translator('en');
$policy=new RollPolicy($settings,$translator);
$post=new CommentPost;$post->parsed_content=$parse('[1,20] [-5,5] [0,0]');
$before=$post->getAttribute('edge_dice');$render=new RenderDice;
$pending=$render($f['renderer'],$post,$post->parsed_content);
check(!str_contains($pending,'result='),'preview does not roll');check($post->getAttribute('edge_dice')===$before,'render has no writes');
$policy->creating($post);$ledger=$post->getAttribute('edge_dice');$data=DiceSyntax::ledger($ledger);
check(count($data['rolls'])===3,'three rolls created');check($data['rolls'][2][2]===0,'zero result preserved');
foreach($data['rolls'] as [$a,$b,$r])check($r>=$a&&$r<=$b,'result in range');
$post->exists=true;$post->syncOriginal();
$rolled=$render($f['renderer'],$post,$post->parsed_content);check(substr_count($rolled,'state="rolled"')===3,'render saved results');
check(str_contains($f['renderer']->render($rolled),'data-result="0"'),'zero HTML');
for($i=0;$i<20;$i++){check($render($f['renderer'],$post,$post->parsed_content)===$rolled,'repeat renders immutable');}
$post->parsed_content=$parse('Edit surrounding text [1,20] [-5,5] [0,0]');$policy->saving($post);check($post->getAttribute('edge_dice')===$ledger,'text edit preserves rolls');
foreach(['[1,20]','[1,21] [-5,5] [0,0]','[-5,5] [1,20] [0,0]','[1,20] [-5,5] [0,0] [1,6]','> [1,20] [-5,5] [0,0]'] as $bad){$post->parsed_content=$parse($bad);rejects(fn()=>$policy->saving($post),'reject changed sequence');}
$post->parsed_content=$parse('[1,20] [-5,5] [0,0]');$post->setAttribute('edge_dice','forged');$policy->saving($post);check($post->getAttribute('edge_dice')===$ledger,'ignore result overwrite');
$old=new CommentPost;$old->exists=true;$old->setRawAttributes(['content'=>$parse('old [1,6]'),'edge_dice'=>null],true);$old->parsed_content=$parse('edit old [1,20]');$policy->saving($old);check($old->getAttribute('edge_dice')===null,'legacy edit never rolls');
check(str_contains($render($f['renderer'],$old,$old->parsed_content),'state="unrolled"'),'legacy rendered unrolled');
$forged=str_replace('<EDGEDICE ','<EDGEDICE result="999" state="rolled" ',$old->parsed_content);
check(!str_contains($render($f['renderer'],$old,$forged),'result="999"'),'forged XML ignored');
$limit=new CommentPost;$limit->parsed_content=$parse(str_repeat('[1,6] ',51));rejects(fn()=>$policy->creating($limit),'limit rejects whole post');
$bare=new s9e\TextFormatter\Configurator;$bare->rendering->setEngine('PHP');$bare->Litedown;$g=$bare->finalize();
check(str_contains($g['renderer']->render($post->parsed_content),'[1,20]'),'removed plugin shows source');
check(!str_contains($g['renderer']->render($post->parsed_content),'EdgeDice'),'removed plugin no broken component');
$defaults=DiceConfig::defaults();check(DiceConfig::validate($defaults)===$defaults,'default settings valid');
foreach(['maxDice'=>101,'criticalPercent'=>0,'specialNumbers'=>['1'],'priority'=>['special'],'colors'=>['normal'=>['light'=>['text'=>'url(javascript:bad)']]],'bogus'=>true] as $key=>$value){$cfg=$defaults;$cfg[$key]=$value;rejects(fn()=>DiceConfig::validate($cfg),'reject config '.$key);}
check(DiceConfig::decode('invalid')===$defaults,'corrupt settings safe fallback');
foreach([['normal'=>'bad'],['normal'=>['light'=>'bad']],['normal'=>['light'=>['text'=>42]]],['normal'=>['moon'=>[]]]] as $colors){$cfg=$defaults;$cfg['colors']=$colors;rejects(fn()=>DiceConfig::validate($cfg),'reject malformed palette');}
check(DiceConfig::validate(['colors'=>[]])['colors']===$defaults['colors'],'partial palette restores defaults');
$eventValues=['edgeinfinity-dice.config'=>json_encode(['maxDice'=>10])];
$event=new Flarum\Settings\Event\Saving($eventValues);
(new EdgeInfinity\Dice\ValidateSettings)->handle($event);
check(json_decode($event->settings['edgeinfinity-dice.config'],true)['maxDice']===10,'settings event normalizes valid configuration');
$badValues=['edgeinfinity-dice.config'=>'{bad'];
$badEvent=new Flarum\Settings\Event\Saving($badValues);
rejects(fn()=>(new EdgeInfinity\Dice\ValidateSettings)->handle($badEvent),'settings event rejects malformed JSON');
check(DiceSyntax::document('<!DOCTYPE r [<!ENTITY e SYSTEM "file:///etc/passwd">]><r><EDGEDICE>&e;</EDGEDICE></r>')===null,'reject XML external entities');
check(DiceSyntax::ledger('{"v":1,"rolls":[[1,6,9]]}')===null,'reject invalid ledger result');
check(DiceSyntax::ledger('{"v":1,"rolls":{"1":[1,6,3]}}')===null,'reject non-list ledger indices');
foreach(['en','zh-Hans'] as $locale){$messages=Symfony\Component\Yaml\Yaml::parseFile(__DIR__.'/../locale/'.$locale.'.yml');check(isset($messages['edgeinfinity-dice']['forum']['error_locked']),'locale '.$locale);}
echo json_encode(['passed'=>$count,'php'=>PHP_VERSION],JSON_PRETTY_PRINT).PHP_EOL;
