<?php

declare(strict_types=1);

namespace Drupal\digitalia_muni_nfield_agent\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'dm_field_agent' field type.
 *
 * @FieldType(
 *   id = "dm_field_agent",
 *   label = @Translation("Agent"),
 *   description = @Translation("Some description."),
 *   default_widget = "dm_field_agent",
 *   default_formatter = "dm_field_agent_default",
 * )
 */
final class AgentItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings(): array {
    $settings = ['foo' => 'example'];
    return $settings + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data): array {
    $settings = $this->getSettings();

    $element['foo'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Foo'),
      '#default_value' => $settings['foo'],
      '#disabled' => $has_data,
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings(): array {
    $settings = ['bar' => 'example'];
    return $settings + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state): array {
    $settings = $this->getSettings();

    $element['bar'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Bar'),
      '#default_value' => $settings['bar'],
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   * The magic method of getting value ($this->VARIABLE_NAME) falls apart when the class already has a variable with the same name (looking at you, name).
   */
  public function isEmpty(): bool {
    return $this->get('role')->getValue() == NULL && $this->get('agent_type')->getValue() == NULL && $this->get('agent_tid')->getValue() == NULL && $this->get('name')->getValue() == NULL && $this->get('orcid')->getValue() == NULL && $this->get('first_names')->getValue() == NULL && $this->get('last_names')->getValue() == NULL && $this->get('ror')->getValue() == NULL && $this->get('institution_affiliation')->getValue() == NULL && $this->get('department_tid')->getValue() == NULL && $this->get('department')->getValue() == NULL && $this->get('contact')->getValue() == NULL && $this->get('alternative_id')->getValue() == NULL && $this->get('alternative_id_type')->getValue() == NULL && $this->get('link')->getValue() == NULL && $this->get('note')->getValue() == NULL && $this->get('private_note')->getValue() == NULL && $this->get('extra')->getValue() == NULL;
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {

    $properties['role'] = DataDefinition::create('string')
      ->setLabel(t('Role'));
    $properties['agent_type'] = DataDefinition::create('string')
      ->setLabel(t('Agent type'));
    $properties['agent_tid'] = DataDefinition::create('string')
      ->setLabel(t('Agent TID'));
    $properties['name'] = DataDefinition::create('string')
      ->setLabel(t('Name'));
    $properties['orcid'] = DataDefinition::create('string')
      ->setLabel(t('ORCID'));
    $properties['first_names'] = DataDefinition::create('string')
      ->setLabel(t('First names'));
    $properties['last_names'] = DataDefinition::create('string')
      ->setLabel(t('Last names'));
    $properties['ror'] = DataDefinition::create('string')
      ->setLabel(t('ROR'));
    $properties['institution_affiliation'] = DataDefinition::create('string')
      ->setLabel(t('Institution (Affiliation if person)'));
    $properties['department_tid'] = DataDefinition::create('string')
      ->setLabel(t('Department TID'));
    $properties['department'] = DataDefinition::create('string')
      ->setLabel(t('Department'));
    $properties['contact'] = DataDefinition::create('string')
      ->setLabel(t('Contact'));
    $properties['alternative_id'] = DataDefinition::create('string')
      ->setLabel(t('Alternative ID'));
    $properties['alternative_id_type'] = DataDefinition::create('string')
      ->setLabel(t('Alternative ID type'));
    $properties['link'] = DataDefinition::create('uri')
      ->setLabel(t('Link'));
    $properties['note'] = DataDefinition::create('string')
      ->setLabel(t('Note'));
    $properties['private_note'] = DataDefinition::create('string')
      ->setLabel(t('Private note'));
    $properties['extra'] = DataDefinition::create('string')
      ->setLabel(t('Extra'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints(): array {
    $constraints = parent::getConstraints();

    $options['role']['AllowedValues'] = array_keys(AgentItem::allowedRoleValues());


    $options['agent_type']['AllowedValues'] = array_keys(AgentItem::allowedAgentTypeValues());

    if (!$this->isEmpty()) {
      //$options['role']['NotBlank'] = [];
      $options['agent_type']['NotBlank'] = [];
      $options['name']['NotBlank'] = [];
    }

    $options['alternative_id_type']['AllowedValues'] = array_keys(AgentItem::allowedAlternativeIDTypeValues());

    $constraint_manager = \Drupal::typedDataManager()->getValidationConstraintManager();
    $constraints[] = $constraint_manager->create('ComplexData', $options);
    // @todo Add more constraints here.
    return $constraints;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {

    $columns = [
      'role' => [
        'type' => 'varchar',
        'length' => 255,
      ],
      'agent_type' => [
        'type' => 'varchar',
        'length' => 255,
      ],
      'agent_tid' => [
        'type' => 'varchar',
        'length' => 255,
      ],
      'name' => [
        'type' => 'varchar',
        'length' => 255,
      ],
      'orcid' => [
        'type' => 'varchar',
        'length' => 255,
      ],
      'first_names' => [
        'type' => 'text',
        'size' => 'big',
      ],
      'last_names' => [
        'type' => 'text',
        'size' => 'big',
      ],
      'ror' => [
        'type' => 'varchar',
        'length' => 255,
      ],
      'institution_affiliation' => [
        'type' => 'text',
        'size' => 'big',
      ],
      'department_tid' => [
        'type' => 'varchar',
        'length' => 255,
      ],
      'department' => [
        'type' => 'text',
        'size' => 'big',
      ],
      'contact' => [
        'type' => 'text',
        'size' => 'big',
      ],
      'alternative_id' => [
        'type' => 'text',
        'length' => 'big',
      ],
      'alternative_id_type' => [
        'type' => 'varchar',
        'length' => 255,
      ],
      'link' => [
        'type' => 'varchar',
        'length' => 2048,
      ],
      'note' => [
        'type' => 'text',
        'size' => 'big',
      ],
      'private_note' => [
        'type' => 'text',
        'size' => 'big',
      ],
      'extra' => [
        'type' => 'text',
        'size' => 'big',
      ],
    ];

    $schema = [
      'columns' => $columns,
      // @DCG Add indexes here if necessary.
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition): array {

    $random = new Random();

    $values['role'] = array_rand(self::allowedRoleValues());

    $values['agent_type'] = array_rand(self::allowedAgentTypeValues());

    $values['agent_tid'] = $random->word(mt_rand(1, 255));

    $values['name'] = $random->word(mt_rand(1, 255));

    $values['orcid'] = $random->word(mt_rand(1, 255));

    $values['first_names'] = $random->paragraphs(5);

    $values['last_names'] = $random->paragraphs(5);

    $values['ror'] = $random->word(mt_rand(1, 255));

    $values['institution_affiliation'] = $random->paragraphs(5);

    $values['department_tid'] = $random->word(mt_rand(1, 255));

    $values['department'] = $random->paragraphs(5);

    $values['contact'] = $random->paragraphs(5);

    $values['alternative_id'] = $random->word(mt_rand(1, 255));

    $values['alternative_id_type'] = array_rand(self::allowedAlternativeIDTypeValues());

    $tlds = ['com', 'net', 'gov', 'org', 'edu', 'biz', 'info'];
    $domain_length = mt_rand(7, 15);
    $protocol = mt_rand(0, 1) ? 'https' : 'http';
    $www = mt_rand(0, 1) ? 'www' : '';
    $domain = $random->word($domain_length);
    $tld = $tlds[mt_rand(0, (count($tlds) - 1))];
    $values['link'] = "$protocol://$www.$domain.$tld";

    $values['note'] = $random->paragraphs(5);

    $values['private_note'] = $random->paragraphs(5);

    $values['extra'] = $random->paragraphs(5);

    return $values;
  }

  /**
   * Returns allowed values for 'role' sub-field.
   */
  public static function allowedFieldRoleValues(): array {
    return [
      'Contributor' => t('Contributor'),
      'Creator' => t('Creator'),
      'Publisher' => t('Publisher'),
    ];
  }

  /**
   * Returns allowed values for 'Contributor' 'role' sub-field.
   */
  public static function allowedRoleValuesContributor(): array {
    return [
      'ContactPerson'         => t('ContactPerson'),
      'DataCollector'         => t('DataCollector'),
      'DataCurator'           => t('DataCurator'),
      'DataManager'           => t('DataManager'),
      'Distributor'           => t('Distributor'),
      'Editor'                => t('Editor'),
      'HostingInstitution'    => t('HostingInstitution'),
      'Producer'              => t('Producer'),
      'ProjectLeader'         => t('ProjectLeader'),
      'ProjectManager'        => t('ProjectManager'),
      'ProjectMember'         => t('ProjectMember'),
      'RegistrationAgency'    => t('RegistrationAgency'),
      'RegistrationAuthority' => t('RegistrationAuthority'),
      'RelatedPerson'         => t('RelatedPerson'),
      'Researcher'            => t('Researcher'),
      'ResearchGroup'         => t('ResearchGroup'),
      'RightsHolder'          => t('RightsHolder'),
      'Sponsor'               => t('Sponsor'),
      'Supervisor'            => t('Supervisor'),
      'Translator'            => t('Translator'),
      'WorkPackageLeader'     => t('WorkPackageLeader'),
      'Other'                 => t('Other'),
    ];
  }

  /**
   * Returns allowed values for 'Creator' 'role' sub-field.
   */
  public static function allowedRoleValuesCreator(): array {
    return [
      'Creator'                 => t('Creator'),
    ];
  }

  /**
   * Returns allowed values for 'Publisher' 'role' sub-field.
   */
  public static function allowedRoleValuesPublisher(): array {
    return [
      'Publisher'                 => t('Publisher'),
    ];
  }

  /**
   * Returns allowed values for 'role' sub-field.
   */
  public static function allowedRoleValues(): array {
    return [
      'Contributor'           => t('Contributor'),
      'ContactPerson'         => t('ContactPerson'),
      'DataCollector'         => t('DataCollector'),
      'DataCurator'           => t('DataCurator'),
      'DataManager'           => t('DataManager'),
      'Distributor'           => t('Distributor'),
      'Editor'                => t('Editor'),
      'HostingInstitution'    => t('HostingInstitution'),
      'Producer'              => t('Producer'),
      'ProjectLeader'         => t('ProjectLeader'),
      'ProjectManager'        => t('ProjectManager'),
      'ProjectMember'         => t('ProjectMember'),
      'RegistrationAgency'    => t('RegistrationAgency'),
      'RegistrationAuthority' => t('RegistrationAuthority'),
      'RelatedPerson'         => t('RelatedPerson'),
      'Researcher'            => t('Researcher'),
      'ResearchGroup'         => t('ResearchGroup'),
      'RightsHolder'          => t('RightsHolder'),
      'Sponsor'               => t('Sponsor'),
      'Supervisor'            => t('Supervisor'),
      'Translator'            => t('Translator'),
      'WorkPackageLeader'     => t('WorkPackageLeader'),
      'Other'                 => t('Other'),
      'Creator'               => t('Creator'),
      'Publisher'             => t('Publisher'),
    ];
  }

  /**
   * Returns allowed values for 'agent_type' sub-field.
   */
  public static function allowedAgentTypeValues(): array {
    // @todo Update allowed values.
    return [
      'organisation' => t('Organisation'),
      'person' => t('Person'),
    ];
  }

  /**
   * Returns allowed values for 'alternative_id_type' sub-field.
   */
  public static function allowedAlternativeIDTypeValues(): array {
    // @todo Update allowed values.
    return [
      'viaf' => t('VIAF'),
      'other' => t('Other'),
    ];
  }

}
