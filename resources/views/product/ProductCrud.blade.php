@extends('layouts.app')

@section('title', 'Product CRUD')

@php
    $panelClass = 'rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm md:p-8';
    $formClass = 'mt-6 space-y-5 [&_label]:mb-2 [&_label]:block [&_label]:text-sm [&_label]:font-semibold [&_label]:text-slate-700 [&_input]:h-11 [&_input]:w-full [&_input]:rounded-xl [&_input]:border [&_input]:border-slate-300 [&_input]:bg-white [&_input]:px-4 [&_input]:text-sm [&_input]:text-slate-900 [&_input]:shadow-sm [&_input]:outline-none [&_input]:transition [&_input]:focus:border-primary-500 [&_input]:focus:ring-4 [&_input]:focus:ring-primary-500/10 [&_select]:h-11 [&_select]:w-full [&_select]:rounded-xl [&_select]:border [&_select]:border-slate-300 [&_select]:bg-white [&_select]:px-4 [&_select]:text-sm [&_select]:text-slate-900 [&_select]:shadow-sm [&_select]:outline-none [&_select]:transition [&_select]:focus:border-primary-500 [&_select]:focus:ring-4 [&_select]:focus:ring-primary-500/10 [&_textarea]:min-h-[7rem] [&_textarea]:w-full [&_textarea]:rounded-xl [&_textarea]:border [&_textarea]:border-slate-300 [&_textarea]:bg-white [&_textarea]:px-4 [&_textarea]:py-3 [&_textarea]:text-sm [&_textarea]:text-slate-900 [&_textarea]:shadow-sm [&_textarea]:outline-none [&_textarea]:transition [&_textarea]:focus:border-primary-500 [&_textarea]:focus:ring-4 [&_textarea]:focus:ring-primary-500/10';
    $primaryButtonClass = 'inline-flex h-11 items-center justify-center rounded-xl bg-primary-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700';
    $secondaryButtonClass = 'inline-flex h-10 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50';
    $dangerButtonClass = 'inline-flex h-10 items-center justify-center rounded-xl border border-rose-200 bg-rose-50 px-4 text-sm font-semibold text-rose-700 transition hover:bg-rose-100';
    $tableWrapClass = 'overflow-hidden rounded-2xl border border-slate-200';
@endphp

@section('content')
    <div class="mx-auto w-full max-w-none space-y-6 px-4 py-6 sm:px-6 lg:px-8 xl:px-10">
    <section class="rounded-[32px] border border-slate-200 bg-[linear-gradient(135deg,#ffffff_0%,#f8fbff_55%,#dbeafe_100%)] p-6 shadow-sm md:p-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">Catalog Ops</p>
                <h1 class="mt-3 text-3xl font-bold tracking-tight text-slate-950 md:text-4xl">Product CRUD</h1>
                <p class="mt-3 text-sm leading-7 text-slate-600 md:text-base">Create, edit, and delete products with multiple images. Every product has one default variant, and you can add extra variants only if needed.</p>
            </div>
            <div class="grid gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-white/80 bg-white/80 px-4 py-3 shadow-sm backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Products</p>
                    <p class="mt-2 text-2xl font-bold text-slate-950">{{ $products->total() }}</p>
                </div>
                <div class="rounded-2xl border border-white/80 bg-white/80 px-4 py-3 shadow-sm backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Categories</p>
                    <p class="mt-2 text-2xl font-bold text-slate-950">{{ $categories->count() }}</p>
                </div>
                <div class="rounded-2xl border border-white/80 bg-white/80 px-4 py-3 shadow-sm backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Editing</p>
                    <p class="mt-2 text-base font-bold text-slate-950">{{ $editingProduct ? '#'.$editingProduct->id : 'Create mode' }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="{{ $panelClass }}">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-950">Create Product</h2>
                <p class="mt-1 text-sm leading-6 text-slate-500">Start with the main record, then define the default and optional extra variant details.</p>
            </div>
            <span class="inline-flex items-center rounded-full border border-primary-100 bg-primary-50 px-3 py-1 text-xs font-semibold text-primary-700">Catalog intake</span>
        </div>

        <form method="POST" action="{{ url('/products-crud') }}" enctype="multipart/form-data" class="{{ $formClass }}">
            @csrf

            <div class="space-y-2">
                <label for="create_name">Name</label>
                <input id="create_name" name="name" value="{{ old('name') }}" required>
            </div>

            <div class="space-y-2">
                <label for="create_slug">Slug (optional)</label>
                <input id="create_slug" name="slug" value="{{ old('slug') }}">
            </div>

            <div class="space-y-2 lg:col-span-2">
                <label for="create_description">Description</label>
                <textarea id="create_description" name="description" rows="3">{{ old('description') }}</textarea>
            </div>

            <div class="space-y-2">
                <label for="create_category_id">Category</label>
                <select id="create_category_id" name="category_id">
                    <option value="">Select category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((int) old('category_id') === (int) $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-2">
                <label for="create_subcategory_id">Subcategory</label>
                <select id="create_subcategory_id" name="subcategory_id">
                    <option value="">Select subcategory</option>
                    @foreach ($subcategories as $subcategory)
                        <option value="{{ $subcategory->id }}" @selected((int) old('subcategory_id') === (int) $subcategory->id)>
                            {{ $subcategory->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-2">
                <label for="create_base_sku">Base SKU (optional)</label>
                <input id="create_base_sku" name="base_sku" value="{{ old('base_sku') }}">
            </div>

            <div class="space-y-2">
                <label for="create_is_published">Published</label>
                <input id="create_is_published" name="is_published" type="checkbox" value="1" class="!h-4 !w-4 !rounded !border-slate-300 !bg-white !px-0 !shadow-none !ring-0 focus:!border-primary-500 focus:!ring-2 focus:!ring-primary-500/20" @checked(old('is_published'))>
            </div>

            <div class="space-y-2 lg:col-span-2">
                <label for="create_images">Product Images (multiple)</label>
                <input id="create_images" name="images[]" type="file" multiple accept="image/*" class="!block !h-auto !w-full !border-0 !bg-transparent !px-0 !text-sm !text-slate-600 !shadow-none file:mr-4 file:rounded-xl file:border-0 file:bg-slate-100 file:px-4 file:py-2.5 file:font-semibold file:text-slate-700 hover:file:bg-slate-200">
            </div>

            <h3 class="text-lg font-semibold text-slate-950">Default Variant</h3>
            <p class="text-sm text-slate-500">This variant is created with the product.</p>
            <input type="hidden" name="variant_id[]" value="">
            <div class="space-y-2">
                <label for="create_variant_sku_0">SKU</label>
                <input id="create_variant_sku_0" name="variant_sku[]" value="{{ old('variant_sku.0') }}">
            </div>

            <input type="hidden" name="variant_name[]" value="{{ old('variant_name.0', 'Default Variant') }}">
            <p><strong>Variant Name:</strong> Default Variant</p>

            <div class="space-y-2">
                <label for="create_variant_model_0">Model Number</label>
                <input id="create_variant_model_0" name="variant_model_number[]" value="{{ old('variant_model_number.0') }}">
            </div>

            <div class="space-y-2">
                <label for="create_variant_catalog_0">Catalog Number</label>
                <input id="create_variant_catalog_0" name="variant_catalog_number[]" value="{{ old('variant_catalog_number.0') }}">
            </div>

            <div class="space-y-2 md:col-span-2">
                <label for="create_variant_technical_specification_json_0">Technical Specifications JSON</label>
                <input id="create_variant_technical_specification_json_0" name="variant_technical_specification_json[]" value="{{ old('variant_technical_specification_json.0') }}">
            </div>

            <div class="space-y-2">
                <label for="create_variant_min_order_0">Min Order Quantity</label>
                <input id="create_variant_min_order_0" name="variant_min_order_quantity[]" type="number" min="1" value="{{ old('variant_min_order_quantity.0', 1) }}">
            </div>

            <div class="space-y-2">
                <label for="create_variant_max_order_0">Max Order Quantity</label>
                <input id="create_variant_max_order_0" name="variant_max_order_quantity[]" type="number" min="1" value="{{ old('variant_max_order_quantity.0') }}">
            </div>

            <div class="space-y-2">
                <label for="create_variant_retail_price_0">Retail Price</label>
                <input id="create_variant_retail_price_0" name="variant_retail_price[]" type="number" step="0.01" min="0" value="{{ old('variant_retail_price.0', old('variant_price.0')) }}">
            </div>

            <div class="space-y-2">
                <label for="create_variant_public_price_0">Public Price</label>
                <input id="create_variant_public_price_0" name="variant_public_price[]" type="number" step="0.01" min="0" value="{{ old('variant_public_price.0') }}">
            </div>

            <div class="space-y-2">
                <label for="create_variant_logged_in_price_0">Logged-In Price</label>
                <input id="create_variant_logged_in_price_0" name="variant_logged_in_price[]" type="number" step="0.01" min="0" value="{{ old('variant_logged_in_price.0') }}">
            </div>

            <div class="space-y-2">
                <label for="create_variant_dealer_price_0">Dealer Price</label>
                <input id="create_variant_dealer_price_0" name="variant_dealer_price[]" type="number" step="0.01" min="0" value="{{ old('variant_dealer_price.0') }}">
            </div>

            <div class="space-y-2">
                <label for="create_variant_institutional_price_0">Institutional Price</label>
                <input id="create_variant_institutional_price_0" name="variant_institutional_price[]" type="number" step="0.01" min="0" value="{{ old('variant_institutional_price.0') }}">
            </div>

            <div class="space-y-2">
                <label for="create_variant_price_0">Legacy Price (retail fallback)</label>
                <input id="create_variant_price_0" name="variant_price[]" type="number" step="0.01" min="0" value="{{ old('variant_price.0') }}">
            </div>

            <div class="space-y-2">
                <label for="create_variant_stock_0">Stock Quantity</label>
                <input id="create_variant_stock_0" name="variant_stock_quantity[]" type="number" min="0" value="{{ old('variant_stock_quantity.0') }}">
            </div>

            <div class="field">
                <label for="create_variant_active_0">Variant Active</label>
                <select id="create_variant_active_0" name="variant_is_active[]">
                    <option value="1" @selected(old('variant_is_active.0', '1') == '1')>Yes</option>
                    <option value="0" @selected(old('variant_is_active.0') == '0')>No</option>
                </select>
            </div>

            <div class="field">
                <label for="create_variant_attr_name_0">Attribute Name</label>
                <input id="create_variant_attr_name_0" name="variant_attribute_name[]" value="{{ old('variant_attribute_name.0') }}">
            </div>

            <div class="field">
                <label for="create_variant_attr_value_0">Attribute Value</label>
                <input id="create_variant_attr_value_0" name="variant_attribute_value[]" value="{{ old('variant_attribute_value.0') }}">
            </div>

            <div class="space-y-2 rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                <p><strong>Additional Variant (optional)</strong></p>
                <input type="hidden" name="variant_id[]" value="">
                <label for="create_variant_sku_1">SKU</label>
                <input id="create_variant_sku_1" name="variant_sku[]" value="{{ old('variant_sku.1') }}">

                <label for="create_variant_name_1">Variant Name</label>
                <input id="create_variant_name_1" name="variant_name[]" value="{{ old('variant_name.1') }}">

                <label for="create_variant_model_1">Model Number</label>
                <input id="create_variant_model_1" name="variant_model_number[]" value="{{ old('variant_model_number.1') }}">

                <label for="create_variant_catalog_1">Catalog Number</label>
                <input id="create_variant_catalog_1" name="variant_catalog_number[]" value="{{ old('variant_catalog_number.1') }}">

                <label for="create_variant_technical_specification_json_1">Technical Specifications JSON</label>
                <input id="create_variant_technical_specification_json_1" name="variant_technical_specification_json[]" value="{{ old('variant_technical_specification_json.1') }}">

                <label for="create_variant_min_order_1">Min Order Quantity</label>
                <input id="create_variant_min_order_1" name="variant_min_order_quantity[]" type="number" min="1" value="{{ old('variant_min_order_quantity.1', 1) }}">

                <label for="create_variant_max_order_1">Max Order Quantity</label>
                <input id="create_variant_max_order_1" name="variant_max_order_quantity[]" type="number" min="1" value="{{ old('variant_max_order_quantity.1') }}">

                <label for="create_variant_retail_price_1">Retail Price</label>
                <input id="create_variant_retail_price_1" name="variant_retail_price[]" type="number" step="0.01" min="0" value="{{ old('variant_retail_price.1', old('variant_price.1')) }}">

                <label for="create_variant_public_price_1">Public Price</label>
                <input id="create_variant_public_price_1" name="variant_public_price[]" type="number" step="0.01" min="0" value="{{ old('variant_public_price.1') }}">

                <label for="create_variant_logged_in_price_1">Logged-In Price</label>
                <input id="create_variant_logged_in_price_1" name="variant_logged_in_price[]" type="number" step="0.01" min="0" value="{{ old('variant_logged_in_price.1') }}">

                <label for="create_variant_dealer_price_1">Dealer Price</label>
                <input id="create_variant_dealer_price_1" name="variant_dealer_price[]" type="number" step="0.01" min="0" value="{{ old('variant_dealer_price.1') }}">

                <label for="create_variant_institutional_price_1">Institutional Price</label>
                <input id="create_variant_institutional_price_1" name="variant_institutional_price[]" type="number" step="0.01" min="0" value="{{ old('variant_institutional_price.1') }}">

                <label for="create_variant_price_1">Legacy Price (retail fallback)</label>
                <input id="create_variant_price_1" name="variant_price[]" type="number" step="0.01" min="0" value="{{ old('variant_price.1') }}">

                <label for="create_variant_stock_1">Stock Quantity</label>
                <input id="create_variant_stock_1" name="variant_stock_quantity[]" type="number" min="0" value="{{ old('variant_stock_quantity.1') }}">

                <label for="create_variant_active_1">Variant Active</label>
                <select id="create_variant_active_1" name="variant_is_active[]">
                    <option value="1" @selected(old('variant_is_active.1', '1') == '1')>Yes</option>
                    <option value="0" @selected(old('variant_is_active.1') == '0')>No</option>
                </select>

                <label for="create_variant_attr_name_1">Attribute Name</label>
                <input id="create_variant_attr_name_1" name="variant_attribute_name[]" value="{{ old('variant_attribute_name.1') }}">

                <label for="create_variant_attr_value_1">Attribute Value</label>
                <input id="create_variant_attr_value_1" name="variant_attribute_value[]" value="{{ old('variant_attribute_value.1') }}">
            </div>

            <button type="submit" class="{{ $primaryButtonClass }}">Create Product</button>
        </form>
    </section>

    @if ($editingProduct)
        <section class="{{ $panelClass }}">
            <h2 class="text-xl font-semibold text-slate-950">Edit Product #{{ $editingProduct->id }}</h2>

            <form method="POST" action="{{ url('/products-crud/'.$editingProduct->id) }}" enctype="multipart/form-data" class="{{ $formClass }}">
                @csrf
                @method('PUT')

                <div class="space-y-2">
                    <label for="edit_name">Name</label>
                    <input id="edit_name" name="name" value="{{ old('name', $editingProduct->name) }}" required>
                </div>

                <div class="space-y-2">
                    <label for="edit_slug">Slug (optional)</label>
                    <input id="edit_slug" name="slug" value="{{ old('slug', $editingProduct->slug) }}">
                </div>

                <div class="space-y-2 lg:col-span-2">
                    <label for="edit_description">Description</label>
                    <textarea id="edit_description" name="description" rows="3">{{ old('description', $editingProduct->description) }}</textarea>
                </div>

                <div class="space-y-2">
                    <label for="edit_category_id">Category</label>
                    <select id="edit_category_id" name="category_id">
                        <option value="">Select category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((int) old('category_id', $editingProduct->category_id) === (int) $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="edit_subcategory_id">Subcategory</label>
                    <select id="edit_subcategory_id" name="subcategory_id">
                        <option value="">Select subcategory</option>
                        @foreach ($subcategories as $subcategory)
                            <option value="{{ $subcategory->id }}" @selected((int) old('subcategory_id', $editingProduct->subcategory_id) === (int) $subcategory->id)>
                                {{ $subcategory->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="edit_base_sku">Base SKU (optional)</label>
                    <input id="edit_base_sku" name="base_sku" value="{{ old('base_sku', $editingProduct->base_sku) }}">
                </div>

                <div class="space-y-2">
                    <label for="edit_is_published">Published</label>
                    <input id="edit_is_published" name="is_published" type="checkbox" value="1" class="!h-4 !w-4 !rounded !border-slate-300 !bg-white !px-0 !shadow-none !ring-0 focus:!border-primary-500 focus:!ring-2 focus:!ring-primary-500/20" @checked(old('is_published', (int) $editingProduct->is_published))>
                </div>

                <div class="space-y-2 lg:col-span-2">
                    <label for="edit_images">Upload Additional Images</label>
                    <input id="edit_images" name="images[]" type="file" multiple accept="image/*" class="!block !h-auto !w-full !border-0 !bg-transparent !px-0 !text-sm !text-slate-600 !shadow-none file:mr-4 file:rounded-xl file:border-0 file:bg-slate-100 file:px-4 file:py-2.5 file:font-semibold file:text-slate-700 hover:file:bg-slate-200">
                </div>

                @if ($editingImages->isNotEmpty())
                    <h3 class="text-lg font-semibold text-slate-950">Existing Images</h3>
                    @foreach ($editingImages as $image)
                        <div class="space-y-3 rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                            <p><strong>Path:</strong> {{ $image->file_path }}</p>
                            <label>
                                <input type="radio" name="primary_image_id" value="{{ $image->id }}" class="!h-4 !w-4 !rounded-full !border-slate-300 !bg-white !px-0 !shadow-none !ring-0 focus:!border-primary-500 focus:!ring-2 focus:!ring-primary-500/20" @checked((int) old('primary_image_id', $editingProduct->product_image_id) === (int) $image->id)>
                                Set as Primary
                            </label>
                            <br>
                            <label>
                                <input type="checkbox" name="delete_image_ids[]" value="{{ $image->id }}" class="!h-4 !w-4 !rounded !border-slate-300 !bg-white !px-0 !shadow-none !ring-0 focus:!border-primary-500 focus:!ring-2 focus:!ring-primary-500/20">
                                Delete this image
                            </label>
                        </div>
                    @endforeach
                @endif

                <h3 class="text-lg font-semibold text-slate-950">Variants</h3>
                @php
                    $variantRows = $editingVariants->count() > 0
                        ? $editingVariants->push((object) ['id' => null, 'sku' => '', 'variant_name' => '', 'technical_specification_json' => '', 'min_order_quantity' => 1, 'max_order_quantity' => '', 'model_number' => '', 'catalog_number' => '', 'public_price' => '', 'logged_in_price' => '', 'retail_price' => '', 'dealer_price' => '', 'institutional_price' => '', 'price' => '', 'stock_quantity' => '', 'is_active' => 1, 'attribute_name' => '', 'attribute_value' => ''])
                        : collect([(object) ['id' => null, 'sku' => '', 'variant_name' => '', 'technical_specification_json' => '', 'min_order_quantity' => 1, 'max_order_quantity' => '', 'model_number' => '', 'catalog_number' => '', 'public_price' => '', 'logged_in_price' => '', 'retail_price' => '', 'dealer_price' => '', 'institutional_price' => '', 'price' => '', 'stock_quantity' => '', 'is_active' => 1, 'attribute_name' => '', 'attribute_value' => '']]);
                @endphp

                @foreach ($variantRows as $index => $variant)
                    @php
                        $isDefaultVariant = $index === 0 && ! empty($variant->id);
                    @endphp
                    <div class="space-y-2 rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                        <p><strong>{{ $isDefaultVariant ? 'Default Variant' : 'Variant '.($index + 1) }}</strong></p>
                        <input type="hidden" name="variant_id[]" value="{{ old('variant_id.'.$index, $variant->id) }}">

                        @if ($isDefaultVariant)
                            <input type="hidden" name="variant_delete[]" value="0">
                            <p class="text-sm text-slate-500">This variant stays with the product.</p>
                        @else
                            <label>Delete Variant</label>
                            <select name="variant_delete[]">
                                <option value="0" @selected((string) old('variant_delete.'.$index, '0') === '0')>No</option>
                                <option value="1" @selected((string) old('variant_delete.'.$index, '0') === '1')>Yes</option>
                            </select>
                        @endif

                        <label>SKU</label>
                        <input name="variant_sku[]" value="{{ old('variant_sku.'.$index, $variant->sku) }}">

                        @if ($isDefaultVariant)
                            <input type="hidden" name="variant_name[]" value="{{ old('variant_name.'.$index, $variant->variant_name ?: 'Default Variant') }}">
                            <p><strong>Variant Name:</strong> {{ old('variant_name.'.$index, $variant->variant_name ?: 'Default Variant') }}</p>
                        @else
                            <label>Variant Name</label>
                            <input name="variant_name[]" value="{{ old('variant_name.'.$index, $variant->variant_name ?? '') }}">
                        @endif

                        <label>Model Number</label>
                        <input name="variant_model_number[]" value="{{ old('variant_model_number.'.$index, $variant->model_number ?? '') }}">

                        <label>Catalog Number</label>
                        <input name="variant_catalog_number[]" value="{{ old('variant_catalog_number.'.$index, $variant->catalog_number ?? '') }}">

                        <label>Technical Specifications JSON</label>
                        <input name="variant_technical_specification_json[]" value="{{ old('variant_technical_specification_json.'.$index, $variant->technical_specification_json ?? '') }}">

                        <label>Min Order Quantity</label>
                        <input name="variant_min_order_quantity[]" type="number" min="1" value="{{ old('variant_min_order_quantity.'.$index, $variant->min_order_quantity ?? 1) }}">

                        <label>Max Order Quantity</label>
                        <input name="variant_max_order_quantity[]" type="number" min="1" value="{{ old('variant_max_order_quantity.'.$index, $variant->max_order_quantity ?? '') }}">

                        <label>Public Price</label>
                        <input name="variant_public_price[]" type="number" step="0.01" min="0" value="{{ old('variant_public_price.'.$index, $variant->public_price ?? '') }}">

                        <label>Logged-In Price</label>
                        <input name="variant_logged_in_price[]" type="number" step="0.01" min="0" value="{{ old('variant_logged_in_price.'.$index, $variant->logged_in_price ?? '') }}">

                        <label>Retail Price</label>
                        <input name="variant_retail_price[]" type="number" step="0.01" min="0" value="{{ old('variant_retail_price.'.$index, $variant->retail_price ?? $variant->price) }}">

                        <label>Dealer Price</label>
                        <input name="variant_dealer_price[]" type="number" step="0.01" min="0" value="{{ old('variant_dealer_price.'.$index, $variant->dealer_price ?? '') }}">

                        <label>Institutional Price</label>
                        <input name="variant_institutional_price[]" type="number" step="0.01" min="0" value="{{ old('variant_institutional_price.'.$index, $variant->institutional_price ?? '') }}">

                        <label>Legacy Price (retail fallback)</label>
                        <input name="variant_price[]" type="number" step="0.01" min="0" value="{{ old('variant_price.'.$index, $variant->price) }}">

                        <label>Stock Quantity</label>
                        <input name="variant_stock_quantity[]" type="number" min="0" value="{{ old('variant_stock_quantity.'.$index, $variant->stock_quantity) }}">

                        <label>Variant Active</label>
                        <select name="variant_is_active[]">
                            <option value="1" @selected((string) old('variant_is_active.'.$index, (string) (int) $variant->is_active) === '1')>Yes</option>
                            <option value="0" @selected((string) old('variant_is_active.'.$index, (string) (int) $variant->is_active) === '0')>No</option>
                        </select>

                        <label>Attribute Name</label>
                        <input name="variant_attribute_name[]" value="{{ old('variant_attribute_name.'.$index, $variant->attribute_name) }}">

                        <label>Attribute Value</label>
                        <input name="variant_attribute_value[]" value="{{ old('variant_attribute_value.'.$index, $variant->attribute_value) }}">
                    </div>
                @endforeach

                <button type="submit" class="{{ $primaryButtonClass }}">Update Product</button>
            </form>

            <form method="POST" action="{{ url('/products-crud/'.$editingProduct->id) }}" class="mt-3">
                @csrf
                @method('DELETE')
                <button type="submit" class="{{ $dangerButtonClass }}" onclick="return confirm('Delete this product?')">Delete Product</button>
            </form>
        </section>
    @endif

    <section class="{{ $panelClass }}">
        <h2 class="text-xl font-semibold text-slate-950">Products</h2>
        <div class="mt-6 {{ $tableWrapClass }}">
            <table class="min-w-full divide-y divide-slate-200 bg-white">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                    <tr>
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Slug</th>
                        <th class="px-4 py-3">Category</th>
                        <th class="px-4 py-3">Subcategory</th>
                        <th class="px-4 py-3">Published</th>
                        <th class="px-4 py-3">Primary Image</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($products as $product)
                        <tr>
                            <td class="px-4 py-4 align-top text-sm font-semibold text-slate-950">{{ $product->id }}</td>
                            <td class="px-4 py-4 align-top text-sm text-slate-700">{{ $product->name }}</td>
                            <td class="px-4 py-4 align-top text-sm text-slate-700">{{ $product->slug }}</td>
                            <td class="px-4 py-4 align-top text-sm text-slate-700">{{ $product->category_name ?? '-' }}</td>
                            <td class="px-4 py-4 align-top text-sm text-slate-700">{{ $product->subcategory_name ?? '-' }}</td>
                            <td class="px-4 py-4 align-top text-sm text-slate-700">
                                <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold {{ (int) $product->is_published === 1 ? 'border-primary-200 bg-primary-50 text-primary-600' : 'border-slate-200 bg-slate-50 text-slate-700' }}">
                                    {{ (int) $product->is_published === 1 ? 'Published' : 'Draft' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 align-top text-xs text-slate-500">{{ $product->primary_image_path ?? '-' }}</td>
                            <td class="px-4 py-4 align-top text-sm text-slate-700">
                                <div class="flex flex-wrap gap-2">
                                    <a class="{{ $secondaryButtonClass }}" href="{{ url('/products-crud?edit_product_id='.$product->id) }}">Edit</a>
                                    <form method="POST" action="{{ url('/products-crud/'.$product->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="{{ $dangerButtonClass }}" onclick="return confirm('Delete this product?')">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <x-ui.table-empty-row
                            colspan="8"
                            title="No products found"
                            description="Add a new product or adjust filters to see results here."
                        />
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-ui.pagination :paginator="$products" class="pt-6" />
    </section>
    </div>
@endsection
