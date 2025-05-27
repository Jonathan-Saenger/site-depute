# Gestion automatique des Timestamps - Article Entity

## Problème résolu

L'erreur suivante se produisait lors de la création d'un nouvel article :
```
SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'created_at' cannot be null
```

## Solution implémentée

### 1. Constructeur automatique
L'entité `Article` a été améliorée avec un constructeur qui initialise automatiquement :
- `createdAt` : défini à la date/heure actuelle lors de l'instanciation
- `isPublished` : défini à `false` par défaut (articles en brouillon)

```php
public function __construct()
{
    $this->createdAt = new \DateTimeImmutable();
    $this->isPublished = false; // Par défaut, les articles sont en brouillon
}
```

### 2. Callback Doctrine PreUpdate
Un callback `@PreUpdate` a été ajouté pour mettre à jour automatiquement le champ `updateAt` lors des modifications :

```php
#[ORM\PreUpdate]
public function updateTimestamp(): void
{
    $this->updateAt = new \DateTime();
}
```

### 3. Annotation HasLifecycleCallbacks
L'entité a été marquée avec `#[ORM\HasLifecycleCallbacks]` pour activer les callbacks Doctrine.

## Avantages de cette approche

### ✅ Code propre et maintenable
- Suit les meilleures pratiques Symfony/Doctrine
- Automatisation des tâches répétitives
- Réduction des erreurs humaines

### ✅ Cohérence des données
- Garantit qu'aucun article ne peut être créé sans `createdAt`
- Assure la traçabilité des modifications
- État par défaut cohérent (brouillon)

### ✅ Simplicité d'utilisation
- Les développeurs n'ont plus à se soucier de définir manuellement ces champs
- Le code du contrôleur reste simple et propre
- Compatible avec les fixtures (possibilité d'override si nécessaire)

## Tests unitaires

5 tests unitaires ont été ajoutés pour valider le comportement :
- ✅ Initialisation automatique du constructeur
- ✅ Validation de la date de création
- ✅ Fonctionnement du callback `updateTimestamp()`
- ✅ Workflow complet de création d'article
- ✅ Gestion des mises à jour multiples

## Migration

Aucune migration de base de données n'est nécessaire car les modifications sont purement au niveau du code PHP.

## Compatibilité

Cette implémentation est compatible avec :
- ✅ Fixtures existantes (peuvent override les valeurs par défaut)
- ✅ Interface d'administration existante
- ✅ API REST (si implémentée)
- ✅ Tests existants
