<?php

declare(strict_types=1);

namespace Drupal\digitalia_muni_nfield_agent\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\digitalia_muni_nfield_agent\Plugin\Field\FieldType\AgentItem;

/**
 * Plugin implementation of the 'dm_field_agent_brief' formatter.
 *
 * @FieldFormatter(
 *   id = "dm_field_agent_brief",
 *   label = @Translation("Brief"),
 *   field_types = {"dm_field_agent"},
 * )
 */
final class AgentBriefFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'name_format' => 'simple_name',
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $element['name_format'] = [
      '#type' => 'select',
      '#title' => $this->t('Name format'),
      '#options' => $this->getNameFormatOptions(),
      '#default_value' => $this->getSetting('name_format'),
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    return [
      $this->t('Name format: @name_format', ['@name_format' => $this->getNameFormatOptions()[$this->getSetting('name_format')]]),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $element = [];

    foreach ($items as $delta => $item) {
      $orcid_ror = "";
      $affiliation = "";
      $department = "";
      $name = "$item->name";

      if ($item->agent_type == "person") {
        if ($item->orcid) {
          $orcid_ror .= " <a href=\"https://orcid.org/{$item->orcid}\" target=\"_blank\"><img src=\"/themes/custom/islandora_muni/images/ORCID-iD_icon_vector.svg\" width=\"16px\"/></a>";
        }

        if ($item->institution_affiliation) {
          $affiliation = "; {$item->institution_affiliation}";
          if (!empty($item->ror)) {
            $affiliation .= " <a href=\"https://ror.org/{$item->ror}\" target=\"_blank\"><img src=\"/themes/custom/islandora_muni/images/ror-icon-rgb.svg\" width=\"20px\"/></a>";
          }
        }
      }

      if ($item->department) {
        $department = ", {$item->department}";
      }

      if ($item->agent_type == "organisation") {
        if ($item->ror) {
          $orcid_ror .= " <a href=\"https://ror.org/{$item->ror}\" target=\"_blank\"><img src=\"/themes/custom/islandora_muni/images/ror-icon-rgb.svg\" width=\"20px\"/></a>";
        }
      }

      if ($item->first_names && $item->last_names) {
        if ($this->getSetting('name_format') == "first_last") {
          $name = "{$item->first_names} {$item->last_names}";
        }
        if ($this->getSetting('name_format') == "last_first") {
          $name = "{$item->last_names}, {$item->first_names}";
        }
      }

      if ($item->name) {
        $element[$delta]['name'] = [
          //'#type' => 'item',
          //'#title' => $this->t('Name'),
          '#markup' => "{$name}{$orcid_ror}{$affiliation}{$department}",
        ];
      }
    }

    return $element;
  }

  protected function getNameFormatOptions() {
    return [
        'simple_name' => "Name field",
        'first_last' => "First-last",
        'last_first' => "Last-first",
    ];
  }
}