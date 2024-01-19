<?php

/*
 * Symfony DataTables Bundle
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Omines\DataTablesBundle\Filter;

use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractFilter
{
    /** @var array<string, mixed> */
    protected array $options;

    public function __construct(array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);
    }

    /**
     * @param array<string, mixed> $options
     */
    public function set(array $options): void
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);
    }

    protected function configureOptions(OptionsResolver $resolver): static
    {
        $resolver
            ->setDefaults([
                'template_html' => null,
                'template_js' => null,
                'leftExpr' => null,
                'operator' => '=',
                'rightExpr' => null,
            ])
            ->setAllowedTypes('template_html', ['null', 'string'])
            ->setAllowedTypes('template_js', ['null', 'string', 'callable'])
            ->setAllowedTypes('operator', ['string'])
            ->setAllowedTypes('leftExpr', ['null', 'string', 'callable'])
            ->setAllowedTypes('rightExpr', ['null', 'string', 'callable'])
        ;

        return $this;
    }

    public function getTemplateHtml(): string
    {
        return $this->options['template_html'];
    }

    public function getTemplateJs(): string
    {
        return $this->options['template_js'];
    }

    public function getLeftExpr(?string $field): mixed
    {
        $leftExpr = $this->options['leftExpr'];
        if (null === $leftExpr) {
            return $field;
        }
        if (is_callable($leftExpr)) {
            return call_user_func($leftExpr, $field);
        }

        return $leftExpr;
    }

    public function getRightExpr(mixed $value): mixed
    {
        $rightExpr = $this->options['rightExpr'];
        if (null === $rightExpr) {
            return $value;
        }
        if (is_callable($rightExpr)) {
            return call_user_func($rightExpr, $value);
        }

        return $rightExpr;
    }

    public function getOperator(): string
    {
        return $this->options['operator'];
    }

    abstract public function isValidValue(mixed $value): bool;
}

