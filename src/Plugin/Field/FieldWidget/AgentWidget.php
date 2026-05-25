<?php

declare(strict_types=1);

namespace Drupal\digitalia_muni_nfield_agent\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\digitalia_muni_nfield_agent\Plugin\Field\FieldType\AgentItem;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\ReplaceCommand;

/**
 * Defines the 'dm_field_agent' field widget.
 *
 * @FieldWidget(
 *   id = "dm_field_agent",
 *   label = @Translation("Agent"),
 *   field_types = {"dm_field_agent"},
 * )
 */
final class AgentWidget extends WidgetBase {
  protected $machine_name;

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return ['role' => 'Creator'] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $element['role'] = [
      '#type' => 'select',
      '#title' => $this->t('Role'),
      '#options' => ['' => $this->t('- Select a value -')] + AgentItem::allowedFieldRoleValues(),
      '#default_value' => $this->getSetting('role'),
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    return [
      $this->t('Role: @role', ['@role' => $this->getSetting('role')]),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    $this->machine_name = $items->getName();
    $this->machine_name_html = str_replace("_", "-", $this->machine_name);

    $element['role'] = [
      '#type' => 'select',
      '#title' => $this->t('Role'),
      '#options' => ['' => $this->t('- Select a value -')],
      //'#default_value' => $items[$delta]->role ?? $this->getSetting('role'),
      '#default_value' => $items[$delta]->role ?? NULL,
    ];

    if ($this->getSetting('role') == 'Contributor') {
      $element['role']['#options'] += AgentItem::allowedRoleValuesContributor();
    }

    if ($this->getSetting('role') == 'Creator') {
      $element['role']['#options'] += AgentItem::allowedRoleValuesCreator();
      $element['role']['#access'] = FALSE;
    }
    if ($this->getSetting('role') == 'Publisher') {
      $element['role']['#options'] += AgentItem::allowedRoleValuesPublisher();
      $element['role']['#access'] = FALSE;
    }

    $element['agent_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Agent type'),
      '#options' => ['' => $this->t('- Select a value -')] + AgentItem::allowedAgentTypeValues(),
      '#default_value' => $items[$delta]->agent_type ?? NULL,
      '#ajax' => [
        'callback' => [$this, 'conditionAgentType'],
        'event' => 'change',
      ],
    ];

    $element['agent_tid'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Agent TID'),
      '#default_value' => $items[$delta]->agent_tid ?? NULL,
      '#states' => [
        'visible' => [
          ":input[data-drupal-selector=edit-{$this->machine_name_html}-{$delta}-agent-type]" => [
            ['value' => 'NEVER'],
          ],
        ],
      ],
    ];

    $element['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#default_value' => $items[$delta]->name ?? NULL,
      '#states' => [
        'visible' => [
          ":input[data-drupal-selector=edit-{$this->machine_name_html}-{$delta}-agent-type]" => [
            ['value' => 'organisation'], 'or' , ['value' => 'person']
          ],
        ],
      ],
    ];

    $element['orcid'] = [
      '#type' => 'textfield',
      '#title' => $this->t('ORCID'),
      '#default_value' => $items[$delta]->orcid ?? NULL,
      '#maxlength' => 1024,
      '#states' => [
        'visible' => [
          ":input[data-drupal-selector=edit-{$this->machine_name_html}-{$delta}-agent-type]" => [
            ['value' => 'person']
          ],
        ],
      ],
      '#autocomplete_route_name' => 'digitalia_muni_autocomplete_remote_orcid.autocomplete',
      '#ajax' => [
        'callback' => [$this, 'populateFieldsORCID'],
        'event' => 'autocompleteclose change'
      ]
    ];

    $element['first_names'] = [
      '#type' => 'textarea',
      '#title' => $this->t('First names'),
      '#default_value' => $items[$delta]->first_names ?? NULL,
      '#rows' => 2,
      '#states' => [
        'visible' => [
          ":input[data-drupal-selector=edit-{$this->machine_name_html}-{$delta}-agent-type]" => [
            ['value' => 'person']
          ],
        ],
      ],
    ];

    $element['last_names'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Last names'),
      '#default_value' => $items[$delta]->last_names ?? NULL,
      '#rows' => 2,
      '#states' => [
        'visible' => [
          ":input[data-drupal-selector=edit-{$this->machine_name_html}-{$delta}-agent-type]" => [
            ['value' => 'person']
          ],
        ],
      ],
    ];

    $element['ror'] = [
      '#type' => 'textfield',
      '#title' => $this->t('ROR'),
      '#default_value' => $items[$delta]->ror ?? NULL,
      '#maxlength' => 8192,
      '#states' => [
        'visible' => [
          ":input[data-drupal-selector=edit-{$this->machine_name_html}-{$delta}-agent-type]" => [
            ['value' => 'person'], 'or', ['value' => 'organisation']
          ],
        ],
      ],
      '#autocomplete_route_name' => 'digitalia_muni_autocomplete_remote_ror.autocomplete',
      '#ajax' => [
        'callback' => [$this, 'populateFieldsROR'],
        'event' => 'autocompleteclose change'
      ]
    ];


    $element['institution_affiliation'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Institution (Affiliation if person)'),
      '#default_value' => $items[$delta]->institution_affiliation ?? NULL,
      '#rows' => 2,
      '#states' => [
        'visible' => [
          ":input[data-drupal-selector=edit-{$this->machine_name_html}-{$delta}-agent-type]" => [
            ['value' => 'person'], 'or', ['value' => 'organisation']
          ],
        ],
      ],
    ];

    $element['department_tid'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Department TID'),
      '#default_value' => $items[$delta]->department_tid ?? NULL,
      '#states' => [
        'visible' => [
          ":input[data-drupal-selector=edit-{$this->machine_name_html}-{$delta}-agent-type]" => [
            ['value' => 'NEVER'],
          ],
        ],
      ],
    ];

    $element['department'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Department'),
      '#default_value' => $items[$delta]->department ?? NULL,
      '#rows' => 2,
      '#states' => [
        'visible' => [
          ":input[data-drupal-selector=edit-{$this->machine_name_html}-{$delta}-agent-type]" => [
            ['value' => 'person'], 'or', ['value' => 'organisation']
          ],
        ],
      ],
    ];

    $element['contact'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Contact'),
      '#default_value' => $items[$delta]->contact ?? NULL,
      '#states' => [
        'visible' => [
          ":input[data-drupal-selector=edit-{$this->machine_name_html}-{$delta}-agent-type]" => [
            ['value' => 'person'], 'or', ['value' => 'organisation']
          ],
        ],
      ],
    ];

    $element['alternative_id_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Alternative ID type'),
      '#options' => ['' => $this->t('- None -')] + AgentItem::allowedAlternativeIDTypeValues(),
      '#default_value' => $items[$delta]->alternative_id_type ?? NULL,
      '#states' => [
        'visible' => [
          ":input[data-drupal-selector=edit-{$this->machine_name_html}-{$delta}-agent-type]" => [
            ['value' => 'person'], 'or', ['value' => 'organisation']
          ],
        ],
      ],
    ];

    $element['alternative_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Alternative ID'),
      '#default_value' => $items[$delta]->alternative_id ?? NULL,
      '#states' => [
        'visible' => [
          [
            ":input[data-drupal-selector=edit-{$this->machine_name_html}-{$delta}-agent-type]" => [
              ['value' => 'person'], 'or', ['value' => 'organisation'],
              
            ],
          ],
          'and',
          [
            ":input[data-drupal-selector=edit-{$this->machine_name_html}-{$delta}-alternative-id-type]" => [
              ['value' => ''],
            ],
          ],
        ],
        'invisible' => [
          [
            ":input[data-drupal-selector=edit-{$this->machine_name_html}-{$delta}-alternative-id-type]" => [
              ['value' => ''],
            ],
          ],
        ],
      ],
    ];

    $element['link'] = [
      '#type' => 'url',
      '#title' => $this->t('Link'),
      '#default_value' => $items[$delta]->link ?? NULL,
      '#states' => [
        'visible' => [
          ":input[data-drupal-selector=edit-{$this->machine_name_html}-{$delta}-agent-type]" => [
            ['value' => 'person'], 'or', ['value' => 'organisation']
          ],
        ],
      ],
    ];

    $element['note'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Note'),
      '#default_value' => $items[$delta]->note ?? NULL,
      '#states' => [
        'visible' => [
          ":input[data-drupal-selector=edit-{$this->machine_name_html}-{$delta}-agent-type]" => [
            ['value' => 'person'], 'or', ['value' => 'organisation']
          ],
        ],
      ],
    ];

    $element['private_note'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Private note'),
      '#default_value' => $items[$delta]->private_note ?? NULL,
      '#states' => [
        'visible' => [
          ":input[data-drupal-selector=edit-{$this->machine_name_html}-{$delta}-agent-type]" => [
            ['value' => 'person'], 'or', ['value' => 'organisation']
          ],
        ],
      ],
    ];

    $element['extra'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Extra'),
      '#default_value' => $items[$delta]->extra ?? NULL,
      '#states' => [
        'visible' => [
          ":input[data-drupal-selector=edit-{$this->machine_name_html}-{$delta}-agent-type]" => [
            ['value' => 'NEVER'],
          ],
        ],
      ],
    ];

    $element['#theme_wrappers'] = ['container', 'form_element'];
    $element['#attributes']['class'][] = 'dm-field-agent-elements';
    $element['#attached']['library'][] = 'digitalia_muni_nfield_agent/dm_field_agent';

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function errorElement(array $element, ConstraintViolationInterface $error, array $form, FormStateInterface $form_state): array|bool {
    $element = parent::errorElement($element, $error, $form, $form_state);
    if ($element === FALSE) {
      return FALSE;
    }
    $error_property = explode('.', $error->getPropertyPath())[1];
    return $element[$error_property];
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state): array {
    foreach ($values as $delta => $value) {
      if ($value['role'] === '') {
        $values[$delta]['role'] = NULL;
      }
      if ($value['agent_type'] === '') {
        $values[$delta]['agent_type'] = NULL;
      }
      if ($value['agent_tid'] === '') {
        $values[$delta]['agent_tid'] = NULL;
      }
      if ($value['name'] === '') {
        $values[$delta]['name'] = NULL;
      }
      if ($value['orcid'] === '') {
        $values[$delta]['orcid'] = NULL;
      }
      if ($value['first_names'] === '') {
        $values[$delta]['first_names'] = NULL;
      }
      if ($value['last_names'] === '') {
        $values[$delta]['last_names'] = NULL;
      }
      if ($value['ror'] === '') {
        $values[$delta]['ror'] = NULL;
      }
      if ($value['institution_affiliation'] === '') {
        $values[$delta]['institution_affiliation'] = NULL;
      }
      if ($value['department_tid'] === '') {
        $values[$delta]['department_tid'] = NULL;
      }
      if ($value['department'] === '') {
        $values[$delta]['department'] = NULL;
      }
      if ($value['contact'] === '') {
        $values[$delta]['contact'] = NULL;
      }
      if ($value['alternative_id'] === '') {
        $values[$delta]['alternative_id'] = NULL;
      }
      if ($value['alternative_id_type'] === '') {
        $values[$delta]['alternative_id_type'] = NULL;
      }
      if ($value['link'] === '') {
        $values[$delta]['link'] = NULL;
      }
      if ($value['note'] === '') {
        $values[$delta]['note'] = NULL;
      }
      if ($value['private_note'] === '') {
        $values[$delta]['private_note'] = NULL;
      }
      if ($value['extra'] === '') {
        $values[$delta]['extra'] = NULL;
      }
    }
    return $values;
  }


  /**
   * Obsolete, hiding done through #states in form.
   */
  public function conditionAgentType(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
  
    $clean_values = $form_state->cleanValues()->getValues();
    $field_html_selector = str_replace("_", "-", $this->machine_name);


    foreach (array_keys($clean_values[$this->machine_name]) as $delta) {
      if ($clean_values[$this->machine_name][$delta]["agent_type"] == "person") {
        //$response->addCommand(new InvokeCommand("[data-drupal-selector=edit-{$field_html_selector}-{$this->delta}-orcid]", "attr", ["disabled", "disabled"]));
        //$response->addCommand(new InvokeCommand("div input[data-drupal-selector=edit-field-creator-0-orcid]", "attr", ["style", "display: none;"]));
        $form["field_creator"]["widget"][$delta]["orcid"]["#access"] = FALSE;
        $form["field_creator"]["widget"][$delta]["orcid"]["#states"] = FALSE;
        \Drupal::logger("DEBUG_ACCESS")->debug(print_r($form["field_creator"]["widget"][$delta]["orcid"]["#access"], TRUE));

        //$response->addCommand(new InvokeCommand("div.form-item--{$field_html_selector}-{$delta}-orcid", "attr", ["style", "display: none;"]));
        //$form[$this->machine_name][field_html_selector}-{$delta}-orcid", "attr", ["style", "display: none;"]));
      } else {
        //$response->addCommand(new InvokeCommand("div.form-item--{$field_html_selector}-{$delta}-orcid", "removeAttr", ["style"]));
      }
    }

    return $response;
  }

  /**
   * TODO, figure it out later, see above function for ajax commands.
   */
  public function populateFieldsORCID(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    $clean_values = $form_state->cleanValues()->getValues();
    $field_html_selector = str_replace("_", "-", $this->machine_name);

    foreach (array_keys($clean_values[$this->machine_name]) as $delta) {
      $decoded = json_decode($clean_values[$this->machine_name][$delta]['orcid'], TRUE);
      $full_name = $decoded["given-names"] . " " . $decoded["family-names"];
      if ($decoded) {
        $response->addCommand(new InvokeCommand("[data-drupal-selector=edit-{$field_html_selector}-{$delta}-first-names]", "val", [$decoded["given-names"]]));
        $response->addCommand(new InvokeCommand("[data-drupal-selector=edit-{$field_html_selector}-{$delta}-first-names]", "attr", ["readonly", "readonly"]));
        $response->addCommand(new InvokeCommand("[data-drupal-selector=edit-{$field_html_selector}-{$delta}-last-names]", "val", [$decoded["family-names"]]));
        $response->addCommand(new InvokeCommand("[data-drupal-selector=edit-{$field_html_selector}-{$delta}-last-names]", "attr", ["readonly", "readonly"]));
        $response->addCommand(new InvokeCommand("[data-drupal-selector=edit-{$field_html_selector}-{$delta}-orcid]", "val", [$decoded["orcid-id"]]));

        $response->addCommand(new InvokeCommand("[data-drupal-selector=edit-{$field_html_selector}-{$delta}-name]", "val", [$full_name]));
      } else {
        $response->addCommand(new InvokeCommand("[data-drupal-selector=edit-{$field_html_selector}-{$delta}-last-names]", "removeAttr", ["readonly"]));
        $response->addCommand(new InvokeCommand("[data-drupal-selector=edit-{$field_html_selector}-{$delta}-first-names]", "removeAttr", ["readonly"]));
      }
    }

    return $response;
  }

  /**
   * TODO, figure it out later, see above function for ajax commands.
   */
  public function populateFieldsROR(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    $clean_values = $form_state->cleanValues()->getValues();
    $field_html_selector = str_replace("_", "-", $this->machine_name);

    foreach (array_keys($clean_values[$this->machine_name]) as $delta) {
      $decoded = json_decode($clean_values[$this->machine_name][$delta]['ror'], TRUE);

      $display_name = "";
      foreach ($decoded["names"]  as $name) {
        if (in_array("ror_display", $name["types"])) {
          $display_name = $name["value"];
          break;
        }
      }

      if ($decoded) {
        $response->addCommand(new InvokeCommand("[data-drupal-selector=edit-{$field_html_selector}-{$delta}-institution-affiliation]", "val", [$display_name]));
        $response->addCommand(new InvokeCommand("[data-drupal-selector=edit-{$field_html_selector}-{$delta}-institution-affiliation]", "attr", ["readonly", "readonly"]));
        $response->addCommand(new InvokeCommand("[data-drupal-selector=edit-{$field_html_selector}-{$delta}-ror]", "val", [$decoded["id"]]));
      } else {
        $response->addCommand(new InvokeCommand("[data-drupal-selector=edit-{$field_html_selector}-{$delta}-institution-affiliation]", "removeAttr", ["readonly"]));

      }
    }

    return $response;
  }
}

