<?php

declare(strict_types=1);
namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Requests;
use GreatMarketrealmCompanion\Core\Http\FormRequest;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\JournalEntry;
defined('ABSPATH') || exit;
final class SaveJournalEntryRequest extends FormRequest
{
 public function authorize(): bool { return current_user_can('gmrc_manage_campaigns'); }
 public function rules(): array { return ['title'=>['required','string','min:2','max:140'],'category'=>['required','string','in:'.implode(',',JournalEntry::CATEGORIES)],'journal_content'=>['required','string','max:20000'],'status'=>['required','string','in:'.implode(',',JournalEntry::STATUSES)],'session_id'=>['string','max:26'],'pinned'=>['string','max:1']]; }
 public function title(): string{return trim($this->validated()->string('title'));} public function category(): string{return $this->validated()->string('category','general');} public function content(): string{return trim($this->validated()->string('journal_content'));} public function status(): string{return $this->validated()->string('status','active');} public function sessionId(): string{return trim($this->validated()->string('session_id'));} public function pinned(): bool{return $this->validated()->string('pinned')==='1';}
}