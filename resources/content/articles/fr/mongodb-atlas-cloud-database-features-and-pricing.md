---
title: MongoDB Atlas : fonctionnalités et tarifs de la base de données cloud
section: digital-horizons
author: michael-anderson
date: 2026-02-23
image: articles/mongodb-atlas-cloud-database-features-and-pricing.webp
---

MongoDB Atlas est un service de base de données multicloud entièrement managé, construit autour de la technologie de base de données orientée documents de MongoDB. Il permet aux développeurs et aux organisations de déployer des bases de données sur Amazon Web Services, Microsoft Azure ou Google Cloud sans avoir à gérer eux-mêmes l'infrastructure de base de données sous-jacente.

Atlas associe le déploiement de bases de données à la surveillance, la sécurité, la mise à l'échelle, les sauvegardes, la recherche, la recherche vectorielle, la fédération de données et d'autres services de développement d'applications. Les organisations peuvent commencer avec un cluster gratuit, utiliser un déploiement Flex facturé à l'usage pour le développement et les tests, ou déployer une infrastructure dédiée pour les charges de travail de production.

La plateforme est conçue pour prendre en charge des applications allant du prototype aux systèmes de production distribués à l'échelle mondiale. Sa tarification repose principalement sur la consommation, les coûts dépendant des ressources du cluster, du stockage, du transfert de données, des sauvegardes, de la région et des services supplémentaires.

## Qu'est-ce que MongoDB Atlas ?

MongoDB Atlas fournit un environnement managé pour déployer et exploiter des bases de données MongoDB dans le cloud. MongoDB gère une grande partie de l'infrastructure de base de données sous-jacente, ce qui permet aux équipes de développement de se concentrer sur les données applicatives et la configuration de la base plutôt que sur la maintenance des serveurs.

Atlas prend en charge les déploiements sur AWS, Azure et Google Cloud, avec plus de 125 régions disponibles selon MongoDB. Les organisations peuvent choisir un fournisseur cloud et une région en fonction de l'architecture de leur application et de leurs contraintes géographiques.

La plateforme prend également en charge les configurations multirégions et multicloud pour les applications qui nécessitent une répartition géographique ou une résilience accrue.

## Options de déploiement d'Atlas

MongoDB Atlas propose actuellement trois grandes catégories de clusters : Free, Flex et Dedicated.

## Clusters Free

L'offre Free permet d'expérimenter MongoDB Atlas sans abonnement payant. Elle comprend 512 Mo de stockage et est destinée à l'apprentissage, à l'expérimentation et aux premières phases de développement.

L'environnement gratuit présente des limites de ressources ; il convient donc généralement aux petits projets plutôt qu'aux charges de travail de production.

## Clusters Flex

Les clusters Flex sont conçus pour le développement d'applications, les tests, les prototypes et les charges de travail à demande relativement faible ou variable.

Ils sont facturés à la consommation et peuvent évoluer automatiquement dans la limite de leurs capacités. MongoDB affiche actuellement un tarif Flex à partir de 0,011 $ de l'heure, l'utilisation pouvant atteindre un maximum de 30 $ par mois.

Flex a remplacé les anciens niveaux de clusters M2 et M5 ainsi que les Atlas Serverless Instances, abandonnés en janvier 2026.

## Clusters Dedicated

Les clusters Dedicated offrent davantage de ressources et de fonctionnalités pour les applications de production.

MongoDB affiche actuellement des tarifs Dedicated à partir de 0,08 $ de l'heure, le niveau M10 démarrant à environ 56,94 $ par mois. Les configurations dédiées vont des déploiements M10 et M20, plus modestes, à des configurations nettement plus importantes offrant jusqu'à 768 Go de RAM et 96 vCPU dans les niveaux standard publiés.

Les organisations peuvent faire évoluer les clusters dédiés verticalement et horizontalement selon les besoins de leurs applications.

## La base de données documentaire MongoDB

Atlas utilise le modèle documentaire de MongoDB, dans lequel les informations sont stockées dans des documents flexibles de type JSON plutôt que dans des lignes et des colonnes traditionnelles.

Cette structure peut être utile pour les applications dont les modèles de données évoluent fréquemment ou dont les informations liées peuvent naturellement être regroupées dans un même document.

Les développeurs peuvent interagir avec Atlas au moyen des pilotes MongoDB, du MongoDB Shell, de Compass, des API et d'intégrations avec les environnements de développement courants.

MongoDB propose également des outils d'indexation et d'optimisation des requêtes qui aident les développeurs à analyser les performances de leurs applications.

## Mise à l'échelle et haute disponibilité

Atlas fournit des fonctionnalités d'infrastructure automatisées conçues pour accompagner la croissance des applications.

Les organisations peuvent configurer des jeux de réplicas, le sharding, des déploiements multirégions et des architectures multicloud selon le type de cluster et leurs besoins. Atlas peut effectuer automatiquement des basculements (failover) et prend en charge l'autoréparation pour les déploiements compatibles.

La mise à l'échelle automatique peut ajuster les ressources de calcul et de stockage en fonction des besoins de la charge de travail. MongoDB fournit également une surveillance et des alertes qui permettent aux équipes de suivre l'utilisation des ressources et les performances de la base de données.

## Clusters globaux et déploiements multirégions

Les organisations qui servent des utilisateurs dans plusieurs zones géographiques peuvent utiliser les Global Clusters et les déploiements multirégions.

Les déploiements globaux permettent de répartir les données et les opérations de base de données entre plusieurs régions, ce qui aide les organisations à rapprocher les données des utilisateurs tout en répondant à certaines exigences de résidence des données.

MongoDB présente les Global Clusters comme permettant des lectures et écritures tenant compte de la localisation, une tolérance aux pannes régionales et des applications géographiquement distribuées.

L'architecture disponible dépend de la configuration de cluster choisie et de la charge de travail.

## Sécurité et protection des données

Les fonctionnalités de sécurité sont intégrées à l'ensemble d'Atlas.

Les organisations peuvent utiliser le chiffrement, l'authentification, les listes d'accès IP, les points de terminaison privés, l'appairage réseau (network peering) et les intégrations d'identité pour contrôler l'accès aux bases de données.

Atlas prend en charge l'authentification via différentes options, notamment des fournisseurs d'identité basés sur SAML, ainsi que l'authentification multifacteur (MFA) pour les utilisateurs d'Atlas. MongoDB assure également le chiffrement des données en transit et au repos, tandis que Queryable Encryption permet d'exécuter certaines requêtes sur des données chiffrées.

Des fonctionnalités de sécurité de niveau supérieur peuvent inclure l'intégration LDAP, l'audit des bases de données et d'autres contrôles destinés aux entreprises.

## MongoDB Search et Vector Search

Atlas va au-delà de sa base de données principale grâce à des services de recherche intégrés.

MongoDB Search offre des fonctionnalités de recherche en texte intégral et par pertinence directement aux côtés des données applicatives. Les développeurs peuvent ainsi créer des fonctions de recherche sans maintenir une infrastructure de recherche entièrement distincte.

MongoDB Vector Search prend en charge la recherche sémantique et les applications d'IA générative. Les développeurs peuvent stocker les données applicatives et leurs représentations vectorielles au sein d'une plateforme intégrée, puis les utiliser pour la récupération d'informations et des expériences fondées sur l'IA.

Ces fonctionnalités peuvent être utiles pour les applications impliquant des systèmes de recommandation, la recherche sémantique, la génération augmentée par récupération (RAG) et d'autres charges de travail d'IA.

## Atlas Stream Processing et Data Federation

Atlas Stream Processing est conçu pour les applications qui doivent traiter des flux continus de données d'événements complexes.

Il peut fonctionner en complément des données de la base opérationnelle, ce qui permet aux organisations de créer des applications combinant traitement d'événements en temps réel et informations applicatives persistantes.

Atlas Data Federation permet aux organisations d'interroger et d'exploiter des données réparties entre Atlas et les environnements de stockage d'objets cloud pris en charge. Cela offre un moyen d'analyser ou de consulter des données sans tout transférer dans un seul cluster de base de données.

## Surveillance, sauvegarde et gestion des données

Atlas fournit des tableaux de bord de surveillance, des alertes, des informations sur les performances et des outils de gestion de base de données.

Les équipes peuvent examiner des indicateurs relatifs à l'état de la base de données et à la consommation des ressources, configurer des alertes et utiliser des suggestions sur les requêtes et les schémas pour repérer des pistes d'optimisation.

Les fonctionnalités de sauvegarde varient selon le type de déploiement. Les clusters dédiés prennent en charge des sauvegardes configurables, des instantanés et des restaurations à un instant donné ou automatisées, tandis que les services de sauvegarde ne sont pas disponibles dans l'offre Free.

Atlas propose également Online Archive, qui peut transférer automatiquement les données rarement consultées vers un stockage d'objets cloud managé tout en conservant la possibilité de les interroger.

## Tarification de MongoDB Atlas

MongoDB Atlas utilise un modèle de tarification à la consommation. Les prix de référence actuels sont les suivants :
* Free : 0 $/heure, avec 512 Mo de stockage
* Flex : 0,011 $/heure, avec une utilisation plafonnée à 30 $/mois
* Dedicated : à partir de 0,08 $/heure
* M10 Dedicated : environ 56,94 $/mois
* M30 Dedicated : environ 0,54 $/heure dans la configuration par défaut publiée

Les coûts réels varient selon le fournisseur cloud, la région, les ressources de calcul, le stockage, les sauvegardes, le transfert de données et les services supplémentaires.

Par exemple, MongoDB estime qu'un déploiement M30 par défaut sur AWS fonctionnant en continu pendant 30 jours coûte environ 388 $ par mois, avant toute modification supplémentaire de la configuration.

## Comment les entreprises évaluent MongoDB Atlas

Les organisations prennent généralement en compte :
* La charge de travail de l'application et la taille de la base de données
* Les performances requises
* Le fournisseur cloud et la région géographique
* Les exigences de haute disponibilité
* Une architecture multirégion ou multicloud
* Les exigences de sécurité et de conformité
* Les besoins en sauvegarde et en restauration
* Les besoins en recherche et en IA
* Les coûts de transfert de données
* Les schémas de croissance attendus
* Les services Atlas supplémentaires

Le déploiement approprié dépend de l'usage de la base de données : expérimentation, développement ou application de production soumise à des exigences élevées de disponibilité et de performance.

## Conclusion

MongoDB Atlas fournit un environnement cloud managé pour les bases de données MongoDB, tout en y ajoutant des fonctionnalités de mise à l'échelle, de sécurité, de surveillance, de recherche, d'applications d'IA, de données en flux continu et de déploiements mondiaux.

L'offre Free peut servir à l'apprentissage et à l'expérimentation, Flex est conçu pour des charges de développement et de test à moindre coût, et les clusters Dedicated offrent davantage de ressources et des fonctionnalités orientées production. Les organisations peuvent aussi combiner Atlas avec MongoDB Search, Vector Search, Stream Processing et Data Federation à mesure que les besoins de leurs applications augmentent.

Comme la tarification d'Atlas dépend fortement de la consommation de ressources et de l'architecture de déploiement, les entreprises doivent évaluer les charges de travail attendues, les régions, le stockage, les sauvegardes, le transfert de données et les services supplémentaires pour estimer le coût total de leur base de données.

## À propos de l'auteur

Daniel Brooks, 41 ans, est consultant en technologies d'entreprise pour une publication de conseil aux entreprises, spécialisé dans la stratégie data, l'efficacité opérationnelle et la business intelligence. Il aide les organisations à améliorer la gestion de leurs informations et à utiliser les données pour éclairer leurs décisions stratégiques et opérationnelles.

## Références

* [MongoDB — Atlas Pricing](https://www.mongodb.com/pricing)
* [MongoDB — Atlas Database Features](https://www.mongodb.com/products/platform/atlas-database)
* [MongoDB — MongoDB Atlas Documentation](https://www.mongodb.com/docs/atlas/)
* [MongoDB — Atlas FAQ](https://www.mongodb.com/docs/atlas/reference/faq/)
* [MongoDB — MongoDB Cloud Services](https://www.mongodb.com/cloud)
* [MongoDB — Atlas Cluster Types](https://www.mongodb.com/docs/atlas/explore/cluster-types/)
* [MongoDB — Atlas Billing and Invoice Breakdown](https://www.mongodb.com/docs/atlas/billing/invoice/)
