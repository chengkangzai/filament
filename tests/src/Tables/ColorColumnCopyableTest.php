<?php

use Filament\Tables\Columns\ColorColumn;
use Filament\Tests\Models\Post;
use Filament\Tests\Tables\Fixtures\PostsTable;
use Filament\Tests\Tables\TestCase;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use function Pest\Livewire\livewire;

uses(TestCase::class);

/**
 * Test fixture for ColorColumn with copyable functionality
 */
class ColorColumnCopyableFixture extends Component implements \Filament\Tables\Contracts\HasTable
{
    use \Filament\Tables\Concerns\InteractsWithTable;

    protected function getTableColumns(): array
    {
        return [
            ColorColumn::make('color_field')
                ->copyable(),
            ColorColumn::make('color_field_with_custom_message')
                ->copyable()
                ->copyMessage('Custom Copied Message')
                ->copyMessageDuration(3000),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return Post::query();
    }

    protected function getTableFilters(): array
    {
        return [];
    }

    protected function getTableActions(): array
    {
        return [];
    }

    protected function getTableBulkActions(): array
    {
        return [];
    }

    public function render(): View
    {
        return view('tables.fixtures.table');
    }
}

it('can render copyable color column', function () {
    Post::factory()->create(['color_field' => '#FF0000']);

    livewire(ColorColumnCopyableFixture::class)
        ->assertCanRenderTableColumn('color_field');
});

it('should have copy handler in the rendered HTML', function () {
    Post::factory()->create(['color_field' => '#FF0000']);

    $component = livewire(ColorColumnCopyableFixture::class)->instance();
    $column = $component->getCachedTableColumn('color_field');
    $record = $component->getTableRecord(Post::first());

    $column->record($record);
    $html = $column->toHtml();

    // Verify the copy handler is present
    expect($html)->toContain('x-on:click');
    expect($html)->toContain('clipboard.writeText');
    expect($html)->toContain('$tooltip');
    // Verify async/await is used for proper promise handling
    expect($html)->toContain('async () => {');
    expect($html)->toContain('await');
    expect($html)->toContain('try {');
    expect($html)->toContain('catch');
});

it('should use custom copy message when provided', function () {
    Post::factory()->create(['color_field' => '#FF0000']);

    $component = livewire(ColorColumnCopyableFixture::class)->instance();
    $column = $component->getCachedTableColumn('color_field_with_custom_message');

    expect($column->getCopyMessage())->toBe('Custom Copied Message');
    expect($column->getCopyMessageDuration())->toBe(3000);
});

it('should encode state correctly for JavaScript', function () {
    Post::factory()->create(['color_field' => '#FF0000']);

    $component = livewire(ColorColumnCopyableFixture::class)->instance();
    $column = $component->getCachedTableColumn('color_field');
    $record = $component->getTableRecord(Post::first());

    $column->record($record);
    $html = $column->toHtml();

    // The state should be properly encoded in the HTML
    expect($html)->toContain('#FF0000');
});

it('should handle special characters in copy message', function () {
    Post::factory()->create(['color_field' => '#FF0000']);

    $component = livewire(ColorColumnCopyableFixture::class)->instance();
    $column = $component->getCachedTableColumn('color_field');

    $column->copyMessage('Copied "value" with \'quotes\'');

    expect($column->getCopyMessage())->toBe('Copied "value" with \'quotes\'');
});
