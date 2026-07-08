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
      'affiliation' => FALSE,
      'contact' => FALSE,
      'link' => FALSE,
      'note' => FALSE,
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $element = [];
    $element['name_format'] = [
      '#type' => 'select',
      '#title' => $this->t('Name format'),
      '#options' => $this->getNameFormatOptions(),
      '#default_value' => $this->getSetting('name_format'),
    ];
    $element['affiliation'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display affiliation and department'),
      '#default_value' => $this->getSetting('affiliation'),
      '#return_value' => 1,
      '#description' => $this->t('If checked, the affiliation and department will be displayed after the name.'),
    ];
    $element['contact'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display contact information'),
      '#default_value' => $this->getSetting('contact'),
      '#return_value' => 1,
      '#description' => $this->t('If checked, contact information will be displayed.'),
    ];
    $element['link'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display link'),
      '#default_value' => $this->getSetting('link'),
      '#return_value' => 1,
      '#description' => $this->t('If checked, a link will be displayed.'),
    ];
    $element['note'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display note'),
      '#default_value' => $this->getSetting('note'),
      '#return_value' => 1,
      '#description' => $this->t('If checked, a note will be displayed.'),
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    return [
      $this->t('Name format: @name_format', ['@name_format' => $this->getNameFormatOptions()[$this->getSetting('name_format')]]),
      $this->t('Display affiliation and department: @affiliation', ['@affiliation' => (bool) $this->getSetting('affiliation') ? 'Yes' : 'No']),
      $this->t('Display contact information: @contact', ['@contact' => (bool) $this->getSetting('contact') ? 'Yes' : 'No']),
      $this->t('Display link: @link', ['@link' => (bool) $this->getSetting('link') ? 'Yes' : 'No']),
      $this->t('Display note: @note', ['@note' => (bool) $this->getSetting('note') ? 'Yes' : 'No']),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $element = [];
    $module_path = \Drupal::service('extension.list.module')->getPath('digitalia_muni_nfield_agent');
    $orcid_icon = Url::fromUri('base:' . $module_path . '/assets/ORCID-iD_icon_unauth_vector.svg')->toString();
    $ror_icon = Url::fromUri('base:' . $module_path . '/assets/ror-icon-rgb.svg')->toString();
    $email_icon = Url::fromUri('base:' . $module_path . '/assets/email.svg')->toString();
    $link_icon = Url::fromUri('base:' . $module_path . '/assets/link.svg')->toString();

    $show_affiliation = filter_var($this->getSetting('affiliation'), FILTER_VALIDATE_BOOLEAN);
    $show_contact = filter_var($this->getSetting('contact'), FILTER_VALIDATE_BOOLEAN);
    $show_link = filter_var($this->getSetting('link'), FILTER_VALIDATE_BOOLEAN);
    $show_note = filter_var($this->getSetting('note'), FILTER_VALIDATE_BOOLEAN);

    foreach ($items as $delta => $item) {
      $orcid_ror = "";
      $affiliation = "";
      $department = "";
      $name = "$item->name";
      $role = "";
      $contact = "";
      $link = "";
      $note = "";

      if ($item->agent_type == "organisation") {
        if ($item->ror) {
          $orcid_ror .= " <a href=\"https://ror.org/{$item->ror}\" target=\"_blank\"><img src=\"{$ror_icon}\" class=\"dm-field-agent-icon\"/></a>";
        }
      }

      if ($item->department) {
        $department = ", {$item->department}";
      }

      if ($item->agent_type == "person") {
        if ($item->orcid) {
          $orcid_ror .= " <a href=\"https://orcid.org/{$item->orcid}\" target=\"_blank\"><img src=\"{$orcid_icon}\" class=\"dm-field-agent-icon\"/></a>";
        }

        if ($show_affiliation && $item->institution_affiliation) {
          $affiliation = "; {$item->institution_affiliation}";
          if (!empty($item->ror)) {
            $affiliation .= " <a href=\"https://ror.org/{$item->ror}\" target=\"_blank\"><img src=\"{$ror_icon}\" class=\"dm-field-agent-icon\"/></a>";
          }
        }

        if (!$show_affiliation) {
          $department = "";
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

      if ($item->role) {
        $role = "{$item->role}: ";
      }

      if ($show_contact && $item->contact) {
        $contact = " <a href=\"mailto:{$item->contact}\" target=\"_blank\"><img src=\"{$email_icon}\" class=\"dm-field-agent-icon\"/></a> {$item->contact}";
      }

      if ($show_link && $item->link) {
        $link = " <a href=\"{$item->link}\" target=\"_blank\"><img src=\"{$link_icon}\" class=\"dm-field-agent-icon\"/></a><a href=\"{$item->link}\" target=\"_blank\">{$item->link}</a>";
      }

      if ($show_note && $item->note) {
        $note = trim($item->note);
        $note = " ({$note})";
      }

      if ($item->name) {
        $element[$delta]['name'] = [
          //'#type' => 'item',
          //'#title' => $this->t('Name'),
          '#markup' => "{$role}{$name}{$orcid_ror}{$affiliation}{$department}{$contact}{$link}{$note}",
        ];
      }
    }

    $element['#attached']['library'][] = 'digitalia_muni_nfield_agent/dm_field_agent';

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