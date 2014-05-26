<?php

namespace Fenchy\UtilBundle\Locale;

use Symfony\Component\Locale\Locale as base;

/**
 * Description of Locale
 *
 * @author Michał Nowak <mnowak@pgs-soft.com>
 */
class Locale extends base {

    /**
     * Returns non asoc array of the language names for a locale
     *
     * @param string $locale The locale to use for the language names
     *
     * @return array              The language names
     *
     * @throws RuntimeException   When the resource bundles cannot be loaded
     */
    public static function getDisplayLanguagesNames($locale)
    {
        if (!isset(self::$languages[$locale])) {
            $bundle = \ResourceBundle::create($locale, self::getIcuDataDirectory().'/lang');

            if (null === $bundle) {
                throw new \RuntimeException(sprintf('The language resource bundle could not be loaded for locale "%s"', $locale));
            }

            $collator = new \Collator($locale);
            $languages = array();
            $bundleLanguages = $bundle->get('Languages') ?: array();

            foreach ($bundleLanguages as $code => $name) {
                // "mul" is the code for multiple languages
                if ('mul' !== $code) {
                    $languages[] = $name;
                }
            }

            $fallbackLocale = self::getFallbackLocale($locale);
            if (null !== $fallbackLocale) {
                $languages = array_merge(self::getDisplayLanguagesNames($fallbackLocale), $languages);
            }

            self::$languages[$locale] = $languages;
        }

        return self::$languages[$locale];
    }

}

?>
