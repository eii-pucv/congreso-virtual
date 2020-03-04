<?php

namespace App\Traits;

use App\UserMeta;
use Carbon\Carbon;

trait UserMetadateable
{
    protected $metaAttributes = [
        'username', 'facebook_token', 'twitter_token', 'google_token', 'github_token', 'clave_unica_token', 'birthday', 'dni', 'pais', 'region', 'comuna',
        'sector', 'nivel_educacional', 'genero', 'actividad', 'es_experto', 'titulo_profesional', 'estudios_adicionales', 'anios_experiencia_laboral',
        'areas_desempenio', 'temas_trabajo', 'es_organizacion', 'nombre_org', 'email_org', 'enlace_org', 'tiene_per_jur', 'dni_org',
        'rep_legal_org', 'tipo_org'
    ];
    protected $virtualMetaAttributes = [
        'has_facebook_token', 'has_twitter_token', 'has_google_token', 'has_github_token', 'has_clave_unica_token'
    ];
    protected $metaAttributesHidden = ['facebook_token', 'twitter_token', 'google_token', 'github_token', 'clave_unica_token'];
    protected $metaAttributesCasts = [
        'sector' => 'integer',
        'tipo_org' => 'integer',
        'es_experto' => 'boolean',
        'es_organizacion' => 'boolean',
        'tiene_per_jur' => 'boolean',
        'estudios_adicionales' => 'json',
        'areas_desempenio' => 'json',
        'temas_trabajo' => 'json'
    ];
    private $tempMetaAttributes = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable(array_merge($this->fillable, $this->metaAttributes));
        $this->append(array_merge($this->metaAttributes, $this->virtualMetaAttributes));
        $this->addHidden($this->metaAttributesHidden);
        $this->casts = array_merge($this->casts, $this->metaAttributesCasts);
    }

    public function setAttribute($key, $value)
    {
        if(in_array($key, $this->metaAttributes)) {
            $this->setMetaAttribute($key, $value);
        } else if(!in_array($key, $this->virtualMetaAttributes)){
            parent::setAttribute($key, $value);
        }
    }

    private function setMetaAttribute($attributeName, $value)
    {
        $metaAttribute = UserMeta::where([
            ['key', $attributeName],
            ['user_id', parent::getKey()]
        ])->first();

        $metaAttributeCreatedAt = null;
        if($metaAttribute) {
            $metaAttributeCreatedAt = $metaAttribute->created_at;
            if($metaAttribute->value != $value) {
                $metaAttribute->forceDelete();
            } else {
                return;
            }
        }
        if($value === null) {
            return;
        }

        $now = Carbon::now('utc')->toDateTimeString();
        $this->tempMetaAttributes[] = [
            'key' => $attributeName,
            'value' => $value,
            'user_id' => $this->id,
            'created_at'=> $metaAttributeCreatedAt ?: $now,
            'updated_at'=> $now
        ];
    }

    public function getUsernameAttribute() { return $this->getMetaAttribute('username'); }
    public function getFacebookTokenAttribute() { return $this->getMetaAttribute('facebook_token'); }
    public function getTwitterTokenAttribute() { return $this->getMetaAttribute('twitter_token'); }
    public function getGoogleTokenAttribute() { return $this->getMetaAttribute('google_token'); }
    public function getGithubTokenAttribute() { return $this->getMetaAttribute('github_token'); }
    public function getClaveUnicaTokenAttribute() { return $this->getMetaAttribute('clave_unica_token'); }
    public function getBirthdayAttribute() { return $this->getMetaAttribute('birthday'); }
    public function getDniAttribute() { return $this->getMetaAttribute('dni'); }
    public function getPaisAttribute() { return $this->getMetaAttribute('pais'); }
    public function getRegionAttribute() { return $this->getMetaAttribute('region'); }
    public function getComunaAttribute() { return $this->getMetaAttribute('comuna'); }
    public function getSectorAttribute() { return $this->getMetaAttribute('sector'); }
    public function getNivelEducacionalAttribute() { return $this->getMetaAttribute('nivel_educacional'); }
    public function getGeneroAttribute() { return $this->getMetaAttribute('genero'); }
    public function getActividadAttribute() { return $this->getMetaAttribute('actividad'); }
    public function getEsExpertoAttribute() { return $this->getMetaAttribute('es_experto'); }
    public function getTituloProfesionalAttribute() { return $this->getMetaAttribute('titulo_profesional'); }
    public function getEstudiosAdicionalesAttribute() { return $this->getMetaAttribute('estudios_adicionales'); }
    public function getAniosExperienciaLaboralAttribute() { return $this->getMetaAttribute('anios_experiencia_laboral'); }
    public function getAreasDesempenioAttribute() { return $this->getMetaAttribute('areas_desempenio'); }
    public function getTemasTrabajoAttribute() { return $this->getMetaAttribute('temas_trabajo'); }
    public function getEsOrganizacionAttribute() { return $this->getMetaAttribute('es_organizacion'); }
    public function getNombreOrgAttribute() { return $this->getMetaAttribute('nombre_org'); }
    public function getEmailOrgAttribute() { return $this->getMetaAttribute('email_org'); }
    public function getEnlaceOrgAttribute() { return $this->getMetaAttribute('enlace_org'); }
    public function getTienePerJurAttribute() { return $this->getMetaAttribute('tiene_per_jur'); }
    public function getDniOrgAttribute() { return $this->getMetaAttribute('dni_org'); }
    public function getRepLegalOrgAttribute() { return $this->getMetaAttribute('rep_legal_org'); }
    public function getTipoOrgAttribute() { return $this->getMetaAttribute('tipo_org'); }

    public function getHasFacebookTokenAttribute() { return $this->getMetaAttribute('facebook_token') ? true : false; }
    public function getHasTwitterTokenAttribute() { return $this->getMetaAttribute('twitter_token') ? true : false; }
    public function getHasGoogleTokenAttribute() { return $this->getMetaAttribute('google_token') ? true : false; }
    public function getHasGithubTokenAttribute() { return $this->getMetaAttribute('github_token') ? true : false; }
    public function getHasClaveUnicaTokenAttribute() { return $this->getMetaAttribute('clave_unica_token') ? true : false; }

    private function getMetaAttribute($attributeName)
    {
        $metaAttribute = UserMeta::where([
            ['key', $attributeName],
            ['user_id', parent::getKey()]
        ])->withTrashed()->first();

        if($metaAttribute) {
            if($this->hasCast($attributeName)) {
                return $this->castAttribute($attributeName, $metaAttribute->value);
            }
            return $metaAttribute->value;
        }
        return null;
    }

    public function save(array $options = [])
    {
        parent::save($options);
        if($this->tempMetaAttributes) {
            UserMeta::insert($this->tempMetaAttributes);
            $this->tempMetaAttributes = [];
        }
    }
}
