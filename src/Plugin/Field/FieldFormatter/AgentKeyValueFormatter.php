<?php

declare(strict_types=1);

namespace Drupal\digitalia_muni_nfield_agent\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\digitalia_muni_nfield_agent\Plugin\Field\FieldType\AgentItem;

/**
 * Plugin implementation of the 'dm_field_agent_key_value' formatter.
 *
 * @FieldFormatter(
 *   id = "dm_field_agent_key_value",
 *   label = @Translation("Key-value"),
 *   field_types = {"dm_field_agent"},
 * )
 */
final class AgentKeyValueFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {

    $element = [];

    foreach ($items as $delta => $item) {
      $table = [
        '#type' => 'table',
      ];

      // Role.
      if ($item->role) {
        $allowed_values = AgentItem::allowedRoleValues();

        $table['#rows'][] = [
          'data' => [
            [
              'header' => TRUE,
              'data' => [
                '#markup' => $this->t('Role'),
              ],
            ],
            [
              'data' => [
                '#markup' => $allowed_values[$item->role],
              ],
            ],
          ],
          'no_striping' => TRUE,
        ];
      }

      // Agent type.
      if ($item->agent_type) {
        $allowed_values = AgentItem::allowedAgentTypeValues();

        $table['#rows'][] = [
          'data' => [
            [
              'header' => TRUE,
              'data' => [
                '#markup' => $this->t('Agent type'),
              ],
            ],
            [
              'data' => [
                '#markup' => $allowed_values[$item->agent_type],
              ],
            ],
          ],
          'no_striping' => TRUE,
        ];
      }

      // Agent TID.
      if ($item->agent_tid) {
        $table['#rows'][] = [
          'data' => [
            [
              'header' => TRUE,
              'data' => [
                '#markup' => $this->t('Agent TID'),
              ],
            ],
            [
              'data' => [
                '#markup' => $item->agent_tid,
              ],
            ],
          ],
          'no_striping' => TRUE,
        ];
      }

      // Name.
      if ($item->name) {
        $table['#rows'][] = [
          'data' => [
            [
              'header' => TRUE,
              'data' => [
                '#markup' => $this->t('Name'),
              ],
            ],
            [
              'data' => [
                '#markup' => $item->name,
              ],
            ],
          ],
          'no_striping' => TRUE,
        ];
      }

      // ORCID.
      if ($item->orcid) {
        $table['#rows'][] = [
          'data' => [
            [
              'header' => TRUE,
              'data' => [
                '#markup' => $this->t('ORCID'),
              ],
            ],
            [
              'data' => [
                '#markup' => $item->orcid,
              ],
            ],
          ],
          'no_striping' => TRUE,
        ];
      }

      // First names.
      if ($item->first_names) {
        $table['#rows'][] = [
          'data' => [
            [
              'header' => TRUE,
              'data' => [
                '#markup' => $this->t('First names'),
              ],
            ],
            [
              'data' => [
                '#markup' => $item->first_names,
              ],
            ],
          ],
          'no_striping' => TRUE,
        ];
      }

      // Last names.
      if ($item->last_names) {
        $table['#rows'][] = [
          'data' => [
            [
              'header' => TRUE,
              'data' => [
                '#markup' => $this->t('Last names'),
              ],
            ],
            [
              'data' => [
                '#markup' => $item->last_names,
              ],
            ],
          ],
          'no_striping' => TRUE,
        ];
      }

      // ROR.
      if ($item->ror) {
        $table['#rows'][] = [
          'data' => [
            [
              'header' => TRUE,
              'data' => [
                '#markup' => $this->t('ROR'),
              ],
            ],
            [
              'data' => [
                '#markup' => $item->ror,
              ],
            ],
          ],
          'no_striping' => TRUE,
        ];
      }

      // Institution (Affiliation if person).
      if ($item->institution_affiliation) {
        $table['#rows'][] = [
          'data' => [
            [
              'header' => TRUE,
              'data' => [
                '#markup' => $this->t('Institution (Affiliation if person)'),
              ],
            ],
            [
              'data' => [
                '#markup' => $item->institution_affiliation,
              ],
            ],
          ],
          'no_striping' => TRUE,
        ];
      }

      // Department TID.
      if ($item->department_tid) {
        $table['#rows'][] = [
          'data' => [
            [
              'header' => TRUE,
              'data' => [
                '#markup' => $this->t('Department TID'),
              ],
            ],
            [
              'data' => [
                '#markup' => $item->department_tid,
              ],
            ],
          ],
          'no_striping' => TRUE,
        ];
      }

      // Department.
      if ($item->department) {
        $table['#rows'][] = [
          'data' => [
            [
              'header' => TRUE,
              'data' => [
                '#markup' => $this->t('Department'),
              ],
            ],
            [
              'data' => [
                '#markup' => $item->department,
              ],
            ],
          ],
          'no_striping' => TRUE,
        ];
      }

      // Contact.
      if ($item->contact) {
        $table['#rows'][] = [
          'data' => [
            [
              'header' => TRUE,
              'data' => [
                '#markup' => $this->t('Contact'),
              ],
            ],
            [
              'data' => [
                '#markup' => $item->contact,
              ],
            ],
          ],
          'no_striping' => TRUE,
        ];
      }

      // Alternative ID.
      if ($item->alternative_id) {
        $table['#rows'][] = [
          'data' => [
            [
              'header' => TRUE,
              'data' => [
                '#markup' => $this->t('Alternative ID'),
              ],
            ],
            [
              'data' => [
                '#markup' => $item->alternative_id,
              ],
            ],
          ],
          'no_striping' => TRUE,
        ];
      }

      // Alternative ID type.
      if ($item->alternative_id_type) {
        $allowed_values = AgentItem::allowedAlternativeIDTypeValues();

        $table['#rows'][] = [
          'data' => [
            [
              'header' => TRUE,
              'data' => [
                '#markup' => $this->t('Alternative ID type'),
              ],
            ],
            [
              'data' => [
                '#markup' => $allowed_values[$item->alternative_id_type],
              ],
            ],
          ],
          'no_striping' => TRUE,
        ];
      }

      // Link.
      if ($item->link) {
        $table['#rows'][] = [
          'data' => [
            [
              'header' => TRUE,
              'data' => [
                '#markup' => $this->t('Link'),
              ],
            ],
            [
              'data' => [
                '#markup' => $item->link,
              ],
            ],
          ],
          'no_striping' => TRUE,
        ];
      }

      // Note.
      if ($item->note) {
        $table['#rows'][] = [
          'data' => [
            [
              'header' => TRUE,
              'data' => [
                '#markup' => $this->t('Note'),
              ],
            ],
            [
              'data' => [
                '#markup' => $item->note,
              ],
            ],
          ],
          'no_striping' => TRUE,
        ];
      }

      // Private note.
      if ($item->private_note) {
        $table['#rows'][] = [
          'data' => [
            [
              'header' => TRUE,
              'data' => [
                '#markup' => $this->t('Private note'),
              ],
            ],
            [
              'data' => [
                '#markup' => $item->private_note,
              ],
            ],
          ],
          'no_striping' => TRUE,
        ];
      }

      // Extra.
      if ($item->extra) {
        $table['#rows'][] = [
          'data' => [
            [
              'header' => TRUE,
              'data' => [
                '#markup' => $this->t('Extra'),
              ],
            ],
            [
              'data' => [
                '#markup' => $item->extra,
              ],
            ],
          ],
          'no_striping' => TRUE,
        ];
      }

      $element[$delta] = $table;
    }

    return $element;
  }

}
