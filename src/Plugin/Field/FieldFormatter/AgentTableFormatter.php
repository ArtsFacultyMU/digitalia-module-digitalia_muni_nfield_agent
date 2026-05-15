<?php

declare(strict_types=1);

namespace Drupal\digitalia_muni_nfield_agent\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\digitalia_muni_nfield_agent\Plugin\Field\FieldType\AgentItem;

/**
 * Plugin implementation of the 'dm_field_agent_table' formatter.
 *
 * @FieldFormatter(
 *   id = "dm_field_agent_table",
 *   label = @Translation("Table"),
 *   field_types = {"dm_field_agent"},
 * )
 */
final class AgentTableFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {

    $header[] = '#';
    $header[] = $this->t('Role');
    $header[] = $this->t('Agent type');
    $header[] = $this->t('Agent TID');
    $header[] = $this->t('Name');
    $header[] = $this->t('ORCID');
    $header[] = $this->t('First names');
    $header[] = $this->t('Last names');
    $header[] = $this->t('ROR');
    $header[] = $this->t('Institution (Affiliation if person)');
    $header[] = $this->t('Department TID');
    $header[] = $this->t('Department');
    $header[] = $this->t('Contact');
    $header[] = $this->t('Alternative ID');
    $header[] = $this->t('Alternative ID type');
    $header[] = $this->t('Link');
    $header[] = $this->t('Note');
    $header[] = $this->t('Private note');
    $header[] = $this->t('Extra');

    $table = [
      '#type' => 'table',
      '#header' => $header,
    ];

    foreach ($items as $delta => $item) {
      $row = [];

      $row[]['#markup'] = $delta + 1;

      if ($item->role) {
        $allowed_values = AgentItem::allowedRoleValues();
        $row[]['#markup'] = $allowed_values[$item->role];
      }
      else {
        $row[]['#markup'] = '';
      }

      if ($item->agent_type) {
        $allowed_values = AgentItem::allowedAgentTypeValues();
        $row[]['#markup'] = $allowed_values[$item->agent_type];
      }
      else {
        $row[]['#markup'] = '';
      }

      $row[]['#markup'] = $item->agent_tid;

      $row[]['#markup'] = $item->name;

      $row[]['#markup'] = $item->orcid;

      $row[]['#markup'] = $item->first_names;

      $row[]['#markup'] = $item->last_names;

      $row[]['#markup'] = $item->ror;

      $row[]['#markup'] = $item->institution_affiliation;

      $row[]['#markup'] = $item->department_tid;

      $row[]['#markup'] = $item->department;

      $row[]['#markup'] = $item->contact;

      $row[]['#markup'] = $item->alternative_id;

      if ($item->alternative_id_type) {
        $allowed_values = AgentItem::allowedAlternativeIDTypeValues();
        $row[]['#markup'] = $allowed_values[$item->alternative_id_type];
      }
      else {
        $row[]['#markup'] = '';
      }

      $row[]['#markup'] = $item->link;

      $row[]['#markup'] = $item->note;

      $row[]['#markup'] = $item->private_note;

      $row[]['#markup'] = $item->extra;

      $table[$delta] = $row;
    }

    return [$table];
  }

}
