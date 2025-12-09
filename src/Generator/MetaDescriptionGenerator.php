<?php
/**
 * @author    Daniel Ionașcu
 * @copyright 2025 Daniel Ionașcu
 * @license   MIT
 */

class MetaDescriptionGenerator
{
    public function generateProductMeta(Product $product, $template, Context $context)
    {
        $idLang = (int)$context->language->id;

        $productName = isset($product->name[$idLang]) ? $product->name[$idLang] : $product->name;
        $shopName = $context->shop->name;

        $manufacturer = new Manufacturer($product->id_manufacturer, $idLang);
        $manufacturerName = Validate::isLoadedObject($manufacturer) ? $manufacturer->name : '';

        $priceWithTax = Product::getPriceStatic($product->id, true);
        $price = Tools::displayPrice($priceWithTax, $context->currency);

        $stockStatus = $product->quantity > 0 ? 'In stock' : 'Out of stock';

        $tagline = Configuration::get('SEO_TAGLINE');

        $replacements = [
            '[product_name]' => $productName,
            '[manufacturer_name]' => $manufacturerName,
            '[shop_name]' => $shopName,
            '[price]' => $price,
            '[stock_status]' => $stockStatus,
            '[tagline]' => $tagline,
        ];

        $description = str_replace(array_keys($replacements), array_values($replacements), $template);

        return $this->cleanDescription($description);
    }

    public function generateCategoryMeta(Category $category, $template, Context $context)
    {
        $idLang = (int)$context->language->id;

        $categoryName = isset($category->name[$idLang]) ? $category->name[$idLang] : $category->name;
        $shopName = $context->shop->name;
        $tagline = Configuration::get('SEO_TAGLINE');

        $parentCategory = '';
        if ($category->id_parent && $category->id_parent > 1) {
            $parent = new Category($category->id_parent, $idLang);
            if (Validate::isLoadedObject($parent)) {
                $parentCategory = isset($parent->name[$idLang]) ? $parent->name[$idLang] : $parent->name;
            }
        }

        $replacements = [
            '[category_name]' => $categoryName,
            '[parent_category]' => $parentCategory,
            '[shop_name]' => $shopName,
            '[tagline]' => $tagline,
        ];

        $description = str_replace(array_keys($replacements), array_values($replacements), $template);

        return $this->cleanDescription($description);
    }

    private function cleanDescription($description)
    {
        $description = strip_tags($description);
        $description = preg_replace('/\s+/', ' ', $description);
        $description = trim($description);

        if (strlen($description) > 160) {
            $description = substr($description, 0, 157) . '...';
        }

        return $description;
    }
}
