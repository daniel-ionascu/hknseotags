# HKN SEO Tags

Auto-generates meta descriptions for products and categories using customizable templates.

## Requirements

- PrestaShop 8.0.0 - 9.x

## Features

- Template-based meta description generation
- Multi-language support
- Only fills in missing meta descriptions (won't override existing ones)
- Automatic 160 character limit

## Available Placeholders

**Product pages:**
- `[product_name]` - Product name
- `[manufacturer_name]` - Manufacturer/brand name
- `[shop_name]` - Shop name
- `[price]` - Formatted price with currency
- `[stock_status]` - "In stock" or "Out of stock"
- `[tagline]` - Custom shop tagline

**Category pages:**
- `[category_name]` - Category name
- `[parent_category]` - Parent category name
- `[shop_name]` - Shop name
- `[tagline]` - Custom shop tagline

## Installation

1. Upload the `hknseotags` folder to `/modules/`
2. Install via Back Office > Modules

## Configuration

Go to Modules > HKN SEO Tags > Configure

Set your templates per language:

```
Product: Buy [product_name] at [shop_name]. [price]. [stock_status].
Category: Browse our [category_name] collection at [shop_name].
```

## Author

Daniel Ionascu - danielionascudev@gmail.com

## License

MIT
