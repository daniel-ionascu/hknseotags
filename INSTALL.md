# Installation

## Requirements

- PrestaShop 8.0+
- PHP 7.2.5+

## Install

1. Upload `hkseotags` to `/modules/`
2. Install from Module Manager
3. Configure templates in module settings

## Configuration

Navigate to: Modules > Front Office Features > Dynamic SEO Tags > Configure

### Setup Steps

1. Set product template for each language
2. Set category template for each language
3. Configure shop tagline (optional)
4. Save settings

## Default Templates

**Product:**
```
Buy [product_name] at [shop_name]. [price]. [stock_status].
```

**Category:**
```
Browse our [category_name] collection at [shop_name].
```

## Testing

1. Find product with empty meta description
2. View product page source
3. Check meta description tag
4. Verify placeholders are replaced

For categories:
1. Find category with empty meta description
2. View category page
3. Check meta description in source

## Troubleshooting

**Meta not generating:**
- Check product/category has empty meta description
- Verify template is configured
- Clear cache

**Wrong language:**
- Configure template for correct language
- Check language settings

**Placeholders not replaced:**
- Verify placeholder spelling
- Check template syntax
