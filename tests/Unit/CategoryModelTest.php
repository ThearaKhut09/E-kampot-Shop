<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_auto_generates_slug_from_name_on_create(): void
    {
        $category = Category::create([
            'name'      => 'Fresh Spices',
            'is_active' => true,
        ]);

        $this->assertEquals('fresh-spices', $category->slug);
    }

    /** @test */
    public function it_updates_slug_when_name_changes(): void
    {
        $category = Category::create(['name' => 'Old Category', 'is_active' => true]);
        $category->update(['name' => 'New Category Name']);

        $this->assertEquals('new-category-name', $category->fresh()->slug);
    }

    /** @test */
    public function scope_active_returns_only_active_categories(): void
    {
        Category::create(['name' => 'Active Cat',   'is_active' => true]);
        Category::create(['name' => 'Inactive Cat', 'is_active' => false]);

        $active = Category::active()->get();

        $this->assertCount(1, $active);
        $this->assertEquals('Active Cat', $active->first()->name);
    }

    /** @test */
    public function scope_parents_returns_only_root_categories(): void
    {
        $parent = Category::create(['name' => 'Parent', 'is_active' => true]);
        $child  = Category::create(['name' => 'Child',  'is_active' => true, 'parent_id' => $parent->id]);

        $parents = Category::parents()->get();

        $this->assertCount(1, $parents);
        $this->assertEquals('Parent', $parents->first()->name);
    }

    /** @test */
    public function path_attribute_returns_single_name_for_root_category(): void
    {
        $category = Category::create(['name' => 'Root', 'is_active' => true]);

        $this->assertEquals('Root', $category->path);
    }

    /** @test */
    public function path_attribute_builds_full_hierarchy(): void
    {
        $parent = Category::create(['name' => 'Spices', 'is_active' => true]);
        $child  = Category::create(['name' => 'Pepper', 'is_active' => true, 'parent_id' => $parent->id]);

        $this->assertEquals('Spices > Pepper', $child->path);
    }

    /** @test */
    public function children_relationship_returns_subcategories(): void
    {
        $parent = Category::create(['name' => 'Parent', 'is_active' => true]);
        Category::create(['name' => 'Child 1', 'is_active' => true, 'parent_id' => $parent->id]);
        Category::create(['name' => 'Child 2', 'is_active' => true, 'parent_id' => $parent->id]);

        $this->assertCount(2, $parent->children);
    }

    /** @test */
    public function parent_relationship_returns_parent_category(): void
    {
        $parent = Category::create(['name' => 'Parent', 'is_active' => true]);
        $child  = Category::create(['name' => 'Child',  'is_active' => true, 'parent_id' => $parent->id]);

        $this->assertEquals($parent->id, $child->parent->id);
    }
}
