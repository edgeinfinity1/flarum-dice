<?php
namespace EdgeInfinity\Dice;
use Flarum\Foundation\ValidationException;
use Flarum\Locale\TranslatorInterface;
use Flarum\Post\CommentPost;
use Flarum\Settings\SettingsRepositoryInterface;
final class RollPolicy
{
    public function __construct(private SettingsRepositoryInterface $settings, private TranslatorInterface $translator) {}
    public function creating(CommentPost $post): void
    {
        $ranges=DiceSyntax::ranges($post->parsed_content ?? '');
        $config=DiceConfig::decode($this->settings->get('edgeinfinity-dice.config'));
        if (count($ranges)>$config['maxDice']) $this->fail('too_many',['{count}'=>$config['maxDice']]);
        // Called immediately before INSERT, never from parse/preview/render.
        $rolls=array_map(static fn(array $r): array => [$r[0],$r[1],random_int($r[0],$r[1])],$ranges);
        $post->setAttribute('edge_dice', $rolls ? json_encode(['v'=>1,'rolls'=>$rolls],JSON_THROW_ON_ERROR) : null);
    }
    public function saving(CommentPost $post): void
    {
        if (!$post->exists) return;
        $stored=$post->getRawOriginal('edge_dice');
        $post->setAttribute('edge_dice',$stored); // No API, importer or extension may replace saved results through normal saves.
        if (!$stored || !$post->isDirty('content')) return;
        $ledger=DiceSyntax::ledger($stored);
        if (!$ledger || !DiceSyntax::matches(DiceSyntax::ranges($post->parsed_content ?? ''),$ledger)) $this->fail('locked');
    }
    private function fail(string $key,array $parameters=[]): never
    { throw new ValidationException(['content'=>$this->translator->trans('edgeinfinity-dice.forum.error_'.$key,$parameters)]); }
}
