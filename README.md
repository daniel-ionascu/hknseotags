# HKN Dynamic SEO Tags

Automatically generate meta descriptions for PrestaShop 8/9 products and categories.

## Features

- Auto-generate meta descriptions for empty fields
- Multi-language support
- Customizable templates with placeholders
- Product and category page support

## Installation

1. Copy `hknseotags` folder to `/modules/`
2. Install from Module Manager
3. Configure templates

## Configuration

Go to Modules > Front Office Features > Dynamic SEO Tags > Configure

### Product Template Placeholders

- `[product_name]` - Product name
- `[manufacturer_name]` - Brand/manufacturer
- `[shop_name]` - Store name
- `[price]` - Product price with currency
- `[stock_status]` - In stock or out of stock
- `[tagline]` - Custom shop tagline

### Category Template Placeholders

- `[category_name]` - Category name
- `[parent_category]` - Parent category name
- `[shop_name]` - Store name
- `[tagline]` - Custom shop tagline

## Examples

**Product Template:**
```
Buy [product_name] at [shop_name]. [price]. [stock_status].
```

**Category Template:**
```
Browse our [category_name] collection at [shop_name]. [tagline]
```

## How It Works

1. Module checks product/category page
2. If meta description is empty, generates one
3. Uses configured template with placeholders
4. Replaces placeholders with actual data
5. Limits to 160 characters

## Important

Module only generates descriptions for empty meta fields.
Manual descriptions are never overwritten.

## Version

1.0.0

## Author

Daniel Ionașcu

## License

MIT
