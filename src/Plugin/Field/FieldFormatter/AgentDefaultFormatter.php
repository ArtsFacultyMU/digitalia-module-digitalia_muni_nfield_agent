<?php

declare(strict_types=1);

namespace Drupal\digitalia_muni_nfield_agent\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\digitalia_muni_nfield_agent\Plugin\Field\FieldType\AgentItem;

/**
 * Plugin implementation of the 'dm_field_agent_default' formatter.
 *
 * @FieldFormatter(
 *   id = "dm_field_agent_default",
 *   label = @Translation("Default"),
 *   field_types = {"dm_field_agent"},
 * )
 */
final class AgentDefaultFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return ['foo' => 'bar'] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $element['foo'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Foo'),
      '#default_value' => $this->getSetting('foo'),
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    return [
      $this->t('Foo: @foo', ['@foo' => $this->getSetting('foo')]),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $element = [];

    foreach ($items as $delta => $item) {

      if ($item->role) {
        $allowed_values = AgentItem::allowedRoleValues();
        $element[$delta]['role'] = [
          '#type' => 'item',
          '#title' => $this->t('Role'),
          '#markup' => $allowed_values[$item->role],
        ];
      }

      if ($item->agent_type) {
        $allowed_values = AgentItem::allowedAgentTypeValues();
        $element[$delta]['agent_type'] = [
          '#type' => 'item',
          '#title' => $this->t('Agent type'),
          '#markup' => $allowed_values[$item->agent_type],
        ];
      }

      if ($item->agent_tid) {
        $element[$delta]['agent_tid'] = [
          '#type' => 'item',
          '#title' => $this->t('Agent TID'),
          '#markup' => $item->agent_tid,
        ];
      }

      if ($item->name) {
        $element[$delta]['name'] = [
          '#type' => 'item',
          '#title' => $this->t('Name'),
          '#markup' => $item->name,
        ];
      }

      if ($item->orcid) {
        $element[$delta]['orcid'] = [
          '#type' => 'item',
          '#title' => $this->t('ORCID'),
          '#markup' => $item->orcid,
        ];
      }

      if ($item->first_names) {
        $element[$delta]['first_names'] = [
          '#type' => 'item',
          '#title' => $this->t('First names'),
          '#markup' => $item->first_names,
        ];
      }

      if ($item->last_names) {
        $element[$delta]['last_names'] = [
          '#type' => 'item',
          '#title' => $this->t('Last names'),
          '#markup' => $item->last_names,
        ];
      }

      if ($item->ror) {
        $element[$delta]['ror'] = [
          '#type' => 'item',
          '#title' => $this->t('ROR'),
          '#markup' => $item->ror,
        ];
      }

      if ($item->institution_affiliation) {
        $element[$delta]['institution_affiliation'] = [
          '#type' => 'item',
          '#title' => $this->t('Institution (Affiliation if person)'),
          '#markup' => $item->institution_affiliation,
        ];
      }

      if ($item->department_tid) {
        $element[$delta]['department_tid'] = [
          '#type' => 'item',
          '#title' => $this->t('Department TID'),
          '#markup' => $item->department_tid,
        ];
      }

      if ($item->department) {
        $element[$delta]['department'] = [
          '#type' => 'item',
          '#title' => $this->t('Department'),
          '#markup' => $item->department,
        ];
      }

      if ($item->contact) {
        $element[$delta]['contact'] = [
          '#type' => 'item',
          '#title' => $this->t('Contact'),
          '#markup' => $item->contact,
        ];
      }

      if ($item->alternative_id) {
        $element[$delta]['alternative_id'] = [
          '#type' => 'item',
          '#title' => $this->t('Alternative ID'),
          '#markup' => $item->alternative_id,
        ];
      }

      if ($item->alternative_id_type) {
        $allowed_values = AgentItem::allowedAlternativeIDTypeValues();
        $element[$delta]['alternative_id_type'] = [
          '#type' => 'item',
          '#title' => $this->t('Alternative ID type'),
          '#markup' => $allowed_values[$item->alternative_id_type],
        ];
      }

      if ($item->link) {
        $element[$delta]['link'] = [
          '#type' => 'item',
          '#title' => $this->t('Link'),
          'content' => [
            '#type' => 'link',
            '#title' => $item->link,
            '#url' => Url::fromUri($item->link),
          ],
        ];
      }

      if ($item->note) {
        $element[$delta]['note'] = [
          '#type' => 'item',
          '#title' => $this->t('Note'),
          '#markup' => $item->note,
        ];
      }

      if ($item->private_note) {
        $element[$delta]['private_note'] = [
          '#type' => 'item',
          '#title' => $this->t('Private note'),
          '#markup' => $item->private_note,
        ];
      }

      if ($item->extra) {
        $element[$delta]['extra'] = [
          '#type' => 'item',
          '#title' => $this->t('Extra'),
          '#markup' => $item->extra,
        ];
      }

    }

    return $element;
  }

}
