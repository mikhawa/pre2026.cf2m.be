-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : db
-- Généré le : jeu. 05 mars 2026 à 08:57
-- Version du serveur : 11.4.10-MariaDB-ubu2404
-- Version de PHP : 8.3.30

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Base de données : `pre_cf2m_db`
--
CREATE DATABASE IF NOT EXISTS `pre_cf2m_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci;
USE `pre_cf2m_db`;

-- --------------------------------------------------------

--
-- Structure de la table `comment`
--

DROP TABLE IF EXISTS `comment`;
CREATE TABLE `comment` (
                           `id` int(10) UNSIGNED NOT NULL,
                           `content` longtext NOT NULL,
                           `is_approved` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
                           `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                           `user_id` int(10) UNSIGNED NOT NULL,
                           `works_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `comment`
--

INSERT INTO `comment` (`id`, `content`, `is_approved`, `created_at`, `user_id`, `works_id`) VALUES
                                                                                                (1, 'Et qui voluptatem accusamus. Quis commodi odio modi aut delectus libero id. Vel nemo deserunt cupiditate nulla repudiandae nesciunt itaque. Et nesciunt architecto suscipit.', 1, '2026-03-05 08:35:36', 153, 1),
                                                                                                (2, 'Ut voluptatum accusantium mollitia voluptas rerum asperiores. Eos deserunt blanditiis aut reiciendis vitae architecto. Iure et reiciendis dolor explicabo et explicabo hic. Aut fugiat dicta sint tempora.', 1, '2026-03-05 08:35:36', 133, 1),
                                                                                                (3, 'Perferendis in dolore beatae omnis consequatur nemo sed explicabo. Fugit voluptas aspernatur voluptas possimus blanditiis in. Explicabo ducimus modi et optio placeat iste. Et aut consequuntur corporis beatae et.', 1, '2026-03-05 08:35:36', 142, 1),
                                                                                                (4, 'Et modi tempora voluptatem dolores ullam. Itaque dignissimos consequuntur ex. Dolorem doloribus inventore non distinctio tenetur fugiat. A ab repellendus qui sunt quia.', 1, '2026-03-05 08:35:36', 158, 1),
                                                                                                (5, 'Aut iusto explicabo consequuntur magni alias quisquam nihil quas. Quod modi quas explicabo omnis. Quia qui fugit nobis fuga non. Facere tempora quasi nihil libero eum et sed.', 1, '2026-03-05 08:35:36', 145, 2),
                                                                                                (6, 'Quia velit reprehenderit placeat aut voluptatem expedita et. Quaerat nisi praesentium sint et atque. Quis non vel et dolor inventore.', 1, '2026-03-05 08:35:36', 134, 2),
                                                                                                (7, 'Aut eligendi enim deserunt a. Dolor delectus nulla distinctio nobis veritatis non. Et deleniti nihil exercitationem est aut nesciunt molestias. Beatae nobis vel ab tempore quisquam in.', 0, '2026-03-05 08:35:36', 154, 3),
                                                                                                (8, 'Non architecto animi ea sed animi nostrum. Vitae cupiditate perspiciatis voluptatem odio velit alias illum. Eius sint et laudantium. Voluptate aut unde aliquam laudantium quo ex deserunt. Rerum temporibus eius quae delectus.', 1, '2026-03-05 08:35:36', 135, 3),
                                                                                                (9, 'Ea voluptatem itaque dolor sit modi. Dignissimos ea laborum assumenda dolores minima rerum. Culpa dolor voluptas officia blanditiis.', 0, '2026-03-05 08:35:36', 154, 4),
                                                                                                (10, 'Labore aliquam facilis et quis eum officia voluptates. Id repellat sunt maxime voluptatum explicabo.', 0, '2026-03-05 08:35:36', 132, 5),
                                                                                                (11, 'Eligendi a est quod quaerat excepturi beatae. Omnis ea reiciendis ut autem magnam velit. Voluptatibus accusamus molestiae ut magni.', 1, '2026-03-05 08:35:36', 139, 6),
                                                                                                (12, 'Illo rerum omnis iure. Nostrum ut quia numquam sed aut id ut. Deserunt itaque dolore voluptatem nobis dolorem. Sunt sapiente aut voluptate blanditiis doloribus eos.', 1, '2026-03-05 08:35:36', 149, 6),
                                                                                                (13, 'Non et sit totam et perspiciatis. Porro ut error aut tempore ea aperiam repellendus. Nobis ad veritatis culpa quo porro debitis.', 1, '2026-03-05 08:35:36', 152, 6),
                                                                                                (14, 'Dolorum quia officiis id quaerat vel fugit. Eos sapiente architecto ab aut libero. Eos esse corrupti tenetur.', 1, '2026-03-05 08:35:36', 136, 7),
                                                                                                (15, 'Blanditiis omnis perspiciatis aliquid ea et. Qui sit rerum enim voluptate aspernatur ut voluptatem velit. Quam dolorem non et accusamus. Perferendis quod voluptates ut eius a vero at.', 1, '2026-03-05 08:35:36', 146, 8),
                                                                                                (16, 'Ullam consequatur voluptatem ex sint suscipit nostrum. Laudantium voluptatibus et quia maxime. Voluptatem laboriosam voluptatibus quia aut aut. Porro enim voluptas reprehenderit vel. Quia quo deleniti quam error vitae.', 1, '2026-03-05 08:35:36', 152, 8),
                                                                                                (17, 'Nostrum ea ipsa ea est iusto dignissimos. Quo explicabo atque perferendis rem nobis velit perferendis. Illum fuga dolores aut est consequatur sit eum.', 0, '2026-03-05 08:35:36', 149, 9),
                                                                                                (18, 'Sit sed ea consequatur delectus doloremque labore quisquam. Modi deleniti voluptatum quas ipsa pariatur repudiandae dignissimos. Voluptate alias et in molestiae voluptas est sit. Vitae vero animi ea aut voluptatem et aut.', 0, '2026-03-05 08:35:36', 154, 9),
                                                                                                (19, 'Veniam dolores illum et nisi rerum amet. Voluptatem at iure quasi veniam quas cumque. Repellendus earum sequi qui explicabo sed. Sunt eveniet quos repellendus. Et reprehenderit et necessitatibus dolor.', 0, '2026-03-05 08:35:36', 146, 10),
                                                                                                (20, 'Doloremque omnis voluptas alias quasi voluptates. Possimus vel sunt ut vitae. Facere quas omnis dolorem cupiditate voluptatem.', 1, '2026-03-05 08:35:36', 136, 10),
                                                                                                (21, 'Ipsa cupiditate rerum rem quidem autem. Accusamus rem dolorem dolor fugiat debitis. Aut quidem odio repudiandae ea illo repudiandae.', 0, '2026-03-05 08:35:36', 145, 10),
                                                                                                (22, 'Laboriosam fugit iure earum adipisci et quae. Ducimus in dolorum vitae enim reiciendis est omnis quia. Optio eaque et perferendis excepturi. Sit maxime mollitia non eum eius est aut. Sit voluptatibus explicabo id dicta.', 0, '2026-03-05 08:35:36', 149, 10),
                                                                                                (23, 'Amet sed debitis sed cumque qui omnis. Unde iste neque nemo laborum at non. Fugiat temporibus nam quisquam repellat asperiores veniam optio tempore.', 0, '2026-03-05 08:35:36', 129, 11),
                                                                                                (24, 'Beatae libero deleniti earum et esse atque nesciunt. Voluptate ratione facilis nihil vitae praesentium qui. Sunt dolorum voluptate omnis necessitatibus recusandae. Tempora facilis consectetur nam corrupti.', 1, '2026-03-05 08:35:36', 152, 11),
                                                                                                (25, 'Ut rerum fugiat totam et sed praesentium. Aut veritatis ullam earum. Incidunt et quia accusantium beatae.', 1, '2026-03-05 08:35:36', 133, 11),
                                                                                                (26, 'Quia deserunt vel et aspernatur nam. Qui asperiores omnis alias. Ex eos sint impedit molestiae soluta est eum recusandae. Cum quidem et voluptate est nulla quod sit.', 0, '2026-03-05 08:35:36', 130, 11),
                                                                                                (27, 'Id odio aut placeat occaecati sit cupiditate voluptas. Ex doloribus et natus doloribus voluptatum et. Doloribus expedita pariatur qui. Architecto ea minima est dolor deserunt laborum. Quasi eaque ea asperiores quos fugiat autem dolore.', 1, '2026-03-05 08:35:36', 134, 12),
                                                                                                (28, 'Dolor ipsum fuga ut ut molestiae incidunt eius. Natus libero et dolorem autem. Id ut fugit libero recusandae sit perspiciatis qui inventore. Enim voluptate consequatur eos nam fugit.', 0, '2026-03-05 08:35:36', 149, 12),
                                                                                                (29, 'Officiis unde dolores ipsam ducimus enim. Non iste et explicabo doloribus. Sapiente aut eum fugit asperiores et quas aut. Sit consequuntur veritatis qui voluptatibus eligendi laboriosam ducimus. Tenetur ut quasi magni cumque consequatur assumenda dolor.', 0, '2026-03-05 08:35:36', 133, 12),
                                                                                                (30, 'Sit distinctio dolorem nihil voluptas ea in at et. Qui illum eveniet sequi labore provident ea architecto nihil. Eos et dolores in hic.', 0, '2026-03-05 08:35:36', 144, 13),
                                                                                                (31, 'In distinctio est rerum dignissimos ipsum est. Quae ut quia cumque. Sunt dolorem illo recusandae autem ipsa voluptatem laboriosam aut. Quia temporibus ducimus nulla quia quia.', 0, '2026-03-05 08:35:36', 152, 13),
                                                                                                (32, 'Deleniti aperiam deleniti nobis facilis unde iusto esse eligendi. Natus quibusdam harum voluptatem et iure perspiciatis iusto. Laborum eos dolor voluptas architecto. Rerum quas ut et veniam.', 0, '2026-03-05 08:35:36', 135, 14),
                                                                                                (33, 'Consequatur aut recusandae mollitia ut ut illo quia. Ad et aliquid ut. Unde quis nam aut et enim qui impedit vel. Dolores neque reprehenderit vel quia nam maxime. A accusamus iusto dolorem voluptatem.', 1, '2026-03-05 08:35:36', 159, 14),
                                                                                                (34, 'Magni rem et in nesciunt qui aut. Ullam aut fuga ea quia deserunt delectus. Omnis nulla voluptatibus omnis porro neque non sequi.', 0, '2026-03-05 08:35:36', 152, 14),
                                                                                                (35, 'Omnis aut sit officiis corrupti deleniti. Dolore non quo veniam porro voluptatem molestiae nam. Enim et ipsum temporibus rerum veniam nisi est. Aut dolores nam consequuntur quis corporis officia facere.', 1, '2026-03-05 08:35:36', 132, 15),
                                                                                                (36, 'Qui veritatis tenetur unde nulla debitis illum. Vel ab et architecto dignissimos eos magni ut. Dolorem officiis cum dignissimos pariatur. Reprehenderit sunt sint odio molestiae sequi.', 1, '2026-03-05 08:35:36', 149, 16),
                                                                                                (37, 'Ipsam ipsa saepe qui enim dolore. Quasi iste ea consequatur dolorem accusantium. Eius dolore fugit officia ea asperiores occaecati. Enim rerum perferendis veniam.', 1, '2026-03-05 08:35:36', 139, 16),
                                                                                                (38, 'Eligendi eos aliquid et qui deserunt. Recusandae aspernatur occaecati placeat quia facere non. Non veniam odio non qui.', 1, '2026-03-05 08:35:36', 146, 16),
                                                                                                (39, 'Et quia et ducimus sint non. Iusto nihil voluptate dicta quidem quaerat voluptatibus. Pariatur unde deleniti eos modi fugit modi nam blanditiis. Occaecati quia velit molestiae neque porro nihil.', 1, '2026-03-05 08:35:36', 159, 17),
                                                                                                (40, 'Quod voluptas illum error non. Ea rerum aut dolores adipisci omnis explicabo. Praesentium expedita magni minima qui quod tempore. Fuga et sequi voluptatem possimus asperiores.', 0, '2026-03-05 08:35:36', 151, 17),
                                                                                                (41, 'Mollitia aspernatur earum cumque exercitationem et. Id et architecto dolores totam fugiat. Dignissimos laudantium adipisci molestiae.', 1, '2026-03-05 08:35:36', 134, 18),
                                                                                                (42, 'Deserunt et sapiente fugit culpa nesciunt. Molestiae adipisci voluptatem omnis quis suscipit. Atque sint voluptatem provident quaerat dolorem asperiores. Repellendus rerum error id architecto libero aut.', 0, '2026-03-05 08:35:36', 158, 18),
                                                                                                (43, 'Repellendus ullam delectus neque necessitatibus nemo. Voluptas incidunt ad ea omnis vitae eius. Accusantium recusandae id deleniti mollitia. Repudiandae est quis reprehenderit a libero nostrum ut.', 1, '2026-03-05 08:35:36', 132, 19),
                                                                                                (44, 'Qui non magni iure illo quam ad eaque. Ipsam quos et ut quae sunt ipsam nobis.', 0, '2026-03-05 08:35:36', 136, 19),
                                                                                                (45, 'Velit et aliquid nemo sit. Rerum quaerat quia tenetur optio. Aliquid tempore velit dolor.', 1, '2026-03-05 08:35:36', 153, 19),
                                                                                                (46, 'Soluta unde sit quasi voluptas unde sint est expedita. Nesciunt voluptatem qui recusandae aut. Commodi numquam repellendus est. Sed quidem inventore ratione et qui qui.', 0, '2026-03-05 08:35:36', 146, 19),
                                                                                                (47, 'Necessitatibus iure rem suscipit quia eaque possimus. Dolor corrupti deleniti delectus similique sint ut nesciunt. Est laudantium at et aperiam quo consequuntur. Dolorem ad quam consectetur unde consectetur sit beatae. Voluptatem quo earum tempore distinctio.', 0, '2026-03-05 08:35:36', 142, 20),
                                                                                                (48, 'Voluptate quas velit voluptas quia quas consequatur nam. Non earum sunt ut corporis nemo minima deleniti unde. Dolore impedit iste voluptas minus est eos.', 0, '2026-03-05 08:35:36', 139, 20),
                                                                                                (49, 'Expedita repellendus dolores quisquam minus eveniet est in. Pariatur provident et cupiditate sit a ea repellendus. Eos modi alias similique. Numquam incidunt maxime sunt.', 1, '2026-03-05 08:35:36', 143, 20);

-- --------------------------------------------------------

--
-- Structure de la table `comment_rating`
--

DROP TABLE IF EXISTS `comment_rating`;
CREATE TABLE `comment_rating` (
                                  `rating_id` int(10) UNSIGNED NOT NULL,
                                  `comment_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `contact_message`
--

DROP TABLE IF EXISTS `contact_message`;
CREATE TABLE `contact_message` (
                                   `id` int(10) UNSIGNED NOT NULL,
                                   `nom` varchar(100) NOT NULL,
                                   `email` varchar(180) NOT NULL,
                                   `sujet` varchar(255) NOT NULL,
                                   `message` longtext NOT NULL,
                                   `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                                   `is_read` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
                                   `read_by_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `contact_message`
--

INSERT INTO `contact_message` (`id`, `nom`, `email`, `sujet`, `message`, `created_at`, `is_read`, `read_by_id`) VALUES
                                                                                                                    (1, 'Yilmaz Camille', 'fleur.cornelis@example.com', 'Eligendi rem voluptates ut unde.', 'Voluptate eum quos quaerat veniam. Asperiores alias tempore nihil quidem velit qui ea. Et sint vel quos dicta tempora commodi. Praesentium minima molestias itaque commodi quos.\n\nQui suscipit dolor rerum. Quis et eum incidunt quisquam. Quo consequatur omnis quasi officia rerum neque quaerat sed.', '2026-03-05 08:35:37', 0, NULL),
                                                                                                                    (2, 'Smets Kato', 'juliette.verstraete@example.org', 'Repellendus ipsam fugiat ea.', 'Non quis commodi unde est ipsum ut ut. Dignissimos officia vel quo omnis. Natus at praesentium iure numquam quis consequatur aliquam rerum. Ut vel consequatur nihil velit quod.\n\nEarum sed minus beatae quas explicabo. Qui consequatur inventore exercitationem necessitatibus. Doloremque quae nisi aut dignissimos voluptatem corrupti in. Numquam et magni id nesciunt iste impedit. Harum rerum beatae excepturi sed quam asperiores.', '2026-03-05 08:35:37', 0, NULL),
                                                                                                                    (3, 'Thiry Margot', 'celia59@example.org', 'Vitae sed odit iusto ea quo.', 'Libero omnis recusandae sint pariatur ullam ea doloribus. Saepe quisquam et aut consequatur eum. Voluptatum enim consequatur numquam architecto voluptatem culpa. Cumque et nobis quisquam quae.\n\nRem soluta aut numquam iste. Aut dolorum eos amet. Qui est vitae atque adipisci.', '2026-03-05 08:35:37', 0, NULL),
                                                                                                                    (4, 'François Axel', 'pieters.kato@example.net', 'Sed vitae velit nesciunt exercitationem ut.', 'Asperiores tempora qui numquam magni modi voluptas asperiores. Commodi eum ducimus fuga a.\n\nIn quod quasi qui culpa unde qui a. Omnis beatae libero distinctio aliquam minus id illo. Ab voluptas aut aliquid in nulla.', '2026-03-05 08:35:37', 0, NULL),
                                                                                                                    (5, 'Janssens Nolan', 'roels.nicolas@example.com', 'Corporis libero sed doloribus doloremque in vero.', 'Sint ipsa repellat qui non veritatis. Totam expedita laborum laudantium officia et explicabo deserunt. Incidunt laboriosam ratione commodi.\n\nSimilique ex id aut illo rem consequatur. Totam consequuntur nihil alias provident magni. Voluptate omnis temporibus consequatur autem aut velit. Et aut aut debitis sed repellat quae nihil est. Vel voluptatibus ut perspiciatis amet molestiae voluptate.', '2026-03-05 08:35:37', 0, NULL),
                                                                                                                    (6, 'Vandamme Vince', 'martin.cornelis@example.org', 'Nostrum dicta rerum explicabo sit explicabo magni.', 'Quisquam adipisci sed voluptatibus itaque cupiditate. Aut sed ullam voluptates modi facilis voluptatem esse. Nisi accusamus tenetur quo a recusandae. Totam officia quia explicabo adipisci aut et est.\n\nEveniet impedit aperiam enim voluptatem. Voluptatem vel ut qui laudantium facilis repellendus. Modi et et et autem non.', '2026-03-05 08:35:37', 0, NULL),
                                                                                                                    (7, 'Collignon Tibo', 'jana.libert@example.net', 'Commodi eum voluptas illo autem.', 'Assumenda enim asperiores voluptatem ipsa repellat voluptatem fugiat illum. Aspernatur sint sed quibusdam cumque magnam dolor ipsa. Placeat voluptatem deleniti voluptate dicta. Dignissimos ab veritatis perferendis sed.\n\nSint quo laborum fugit consequuntur et assumenda. Quidem eos illum id saepe.', '2026-03-05 08:35:37', 0, NULL),
                                                                                                                    (8, 'Verbeke Elise', 'dierckx.margot@example.net', 'Amet nobis porro voluptas tempora aut.', 'Quia repellendus a nisi mollitia. Non in voluptas asperiores culpa veniam. Soluta repellendus libero similique ea dignissimos aperiam culpa aut. Eius quidem impedit enim soluta.\n\nMolestias eaque voluptatem a eaque qui sequi soluta. Est ut voluptas aut non voluptatem. Quaerat qui error molestias et.', '2026-03-05 08:35:37', 0, NULL),
                                                                                                                    (9, 'Toussaint Amber', 'lecomte.lotte@example.com', 'Soluta quaerat ea inventore.', 'Suscipit architecto odio quas et modi reiciendis omnis. Quis nam perferendis ducimus quod et expedita et quidem. Ut at eaque debitis explicabo.\n\nNatus eos exercitationem veritatis non voluptatem omnis consequatur. Animi ut excepturi iusto iusto est.', '2026-03-05 08:35:37', 1, NULL),
                                                                                                                    (10, 'Cornelis Lotte', 'wout.maes@example.com', 'Omnis aut libero quis quasi quis.', 'Quis architecto voluptatem corrupti iste. Officia sunt ea eveniet aut voluptatibus ad. Dolorum ea et rem voluptas quod sed.\n\nAut soluta id aut debitis ipsam. Dolores occaecati illum illum error aut voluptatem. Fuga natus odit voluptas voluptate. Impedit est dolor nam voluptatem quod eligendi.', '2026-03-05 08:35:37', 1, NULL),
                                                                                                                    (11, 'Jansen Alexandre', 'gillet.thomas@example.net', 'Rerum sint amet consequatur maxime repellat molestias.', 'Eveniet ut aut commodi ipsum iste aut. Pariatur ducimus qui distinctio dolorem quam vel totam consequatur. Ea et sit fuga beatae.\n\nEst praesentium laboriosam unde inventore laboriosam consectetur quia. Sunt et aliquam nobis veniam eum. Nam quibusdam iusto debitis ut.', '2026-03-05 08:35:37', 1, NULL),
                                                                                                                    (12, 'François Raphaël', 'roos.mertens@example.net', 'Qui ad laboriosam qui vel est.', 'Voluptatem sunt doloribus ab nisi. Et animi et aut veritatis facere deleniti incidunt. Vel quia veniam hic. Id est autem quos placeat et et labore.\n\nQui voluptatem perferendis alias velit aperiam dolores doloremque. Maiores et suscipit debitis sed aspernatur. Fugiat ipsam voluptatum assumenda placeat aliquid. In repudiandae ipsum totam ea veniam possimus omnis. Vel laudantium veniam adipisci ut reiciendis id quo.', '2026-03-05 08:35:37', 1, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE `doctrine_migration_versions` (
                                               `version` varchar(191) NOT NULL,
                                               `executed_at` datetime DEFAULT NULL,
                                               `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
                                                                                           ('DoctrineMigrations\\Version20260304112351', '2026-03-04 11:26:54', 703),
                                                                                           ('DoctrineMigrations\\Version20260304114038', '2026-03-04 11:41:13', 217);

-- --------------------------------------------------------

--
-- Structure de la table `formation`
--

DROP TABLE IF EXISTS `formation`;
CREATE TABLE `formation` (
                             `id` int(10) UNSIGNED NOT NULL,
                             `title` varchar(255) NOT NULL,
                             `slug` varchar(255) NOT NULL,
                             `description` longtext DEFAULT NULL,
                             `status` varchar(20) NOT NULL DEFAULT 'draft',
                             `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                             `published_at` datetime DEFAULT NULL,
                             `updated_at` datetime DEFAULT NULL,
                             `created_by_id` int(10) UNSIGNED NOT NULL,
                             `updated_by_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `formation`
--

INSERT INTO `formation` (`id`, `title`, `slug`, `description`, `status`, `created_at`, `published_at`, `updated_at`, `created_by_id`, `updated_by_id`) VALUES
                                                                                                                                                           (11, 'Eum provident ipsum laudantium perferendis adipisci quod', 'eum-provident-ipsum-laudantium-perferendis-adipisci-quod', 'Beatae architecto omnis unde tempore. Placeat at maxime qui ut. Veniam debitis necessitatibus iusto asperiores in qui impedit ut.\n\nPorro ut perferendis modi. Reprehenderit doloribus aspernatur et voluptatibus natus voluptas sapiente. Blanditiis facilis ipsam sed accusantium et quaerat repudiandae.\n\nQuasi eius porro ducimus quam numquam tenetur. Harum itaque rerum consequatur illo corrupti ut corrupti. Omnis id quia tenetur sit enim consectetur unde.', 'published', '2026-03-05 08:35:35', '2025-11-24 08:30:22', NULL, 133, NULL),
                                                                                                                                                           (12, 'Minima consequatur commodi dignissimos iste', 'minima-consequatur-commodi-dignissimos-iste', 'Et error asperiores et nisi voluptas officiis voluptatem dolores. Architecto saepe et debitis cum nihil et et. Maiores temporibus optio ut nemo eos.\n\nQuia est animi veniam ea amet ut et. Soluta ratione ducimus quas vel eaque assumenda. Iste sequi veritatis numquam molestiae iure.\n\nEst dolores quia sunt deserunt qui nemo ea. Numquam provident perspiciatis veritatis itaque doloribus at qui.', 'published', '2026-03-05 08:35:35', '2025-05-26 07:13:06', NULL, 132, NULL),
                                                                                                                                                           (13, 'Modi ut corrupti autem ut ipsa aut', 'modi-ut-corrupti-autem-ut-ipsa-aut', 'Dolorem eligendi qui dolorum ea rerum et eos. Dolorum sint aut soluta fugiat quam quae hic. Consectetur est quas deserunt facere nobis itaque. Mollitia non ipsa iste ducimus odit consectetur.\n\nFugit enim nulla et consectetur placeat et. Consequatur odit ab voluptas modi et aperiam. Velit omnis voluptas voluptatem ut reprehenderit quis.\n\nCumque quia maiores nam et voluptate fugiat sit perferendis. Ut eveniet ut aut. In deserunt facere libero repudiandae provident est.', 'published', '2026-03-05 08:35:35', '2025-10-17 23:55:47', NULL, 132, NULL),
                                                                                                                                                           (14, 'Quo nam assumenda necessitatibus saepe quis', 'quo-nam-assumenda-necessitatibus-saepe-quis', 'Excepturi unde quos tempore suscipit. Non eveniet sed voluptates quibusdam. Et ut consequatur consequatur labore. Aut autem ex accusamus eligendi officia vitae.\n\nQuos unde veniam ut officia cumque. Id natus est veniam expedita. Officiis similique doloremque dolorem illum consequuntur et. Suscipit eius molestiae accusamus omnis commodi.\n\nMinus iure enim harum. Minima omnis minima quia quis occaecati voluptate. Officia qui delectus aperiam voluptatum et et quo.', 'published', '2026-03-05 08:35:35', NULL, NULL, 135, NULL),
                                                                                                                                                           (15, 'In voluptate ea facilis est ipsum nisi', 'in-voluptate-ea-facilis-est-ipsum-nisi', 'Necessitatibus sint ut maxime voluptas et. Et ullam rerum illo assumenda. Officia molestiae aut voluptatem consequatur. Et qui doloribus consequuntur aut recusandae.\n\nInventore soluta consequatur sit distinctio animi ut. Facilis ut quibusdam nihil. Nesciunt modi nemo dolor molestiae. Harum ea sed architecto ut dolores consectetur vitae. Doloribus tempore blanditiis ab earum labore et.\n\nAtque modi voluptate sit ut non. Necessitatibus dolores exercitationem autem aliquid. Voluptatum voluptatem id quia rerum voluptatem maiores id. Aut blanditiis suscipit doloremque beatae.', 'published', '2026-03-05 08:35:35', '2026-02-02 17:28:17', NULL, 132, NULL),
                                                                                                                                                           (16, 'Sequi corrupti odio ullam', 'sequi-corrupti-odio-ullam', 'Quia rerum quibusdam corrupti mollitia. Excepturi ipsum ratione nesciunt ut rem ullam nesciunt. Et ipsum aut neque quod.\n\nNesciunt veritatis tempora sint aperiam eveniet voluptates. Maxime et delectus doloribus soluta. Et porro ex non ut a eaque.\n\nSoluta facere nihil quo ut consequatur delectus. Illum sed sit quasi illum fugiat consequatur atque quo.', 'published', '2026-03-05 08:35:35', '2025-03-14 04:06:33', NULL, 133, NULL),
                                                                                                                                                           (17, 'Possimus voluptatem veritatis ut ut', 'possimus-voluptatem-veritatis-ut-ut', 'Nisi aliquam illo molestias consequatur ullam non. Laborum et est aut praesentium et quia ut dolore. Dolorem magnam veritatis eum est tempore rem iure cumque. Rerum dolorum molestiae ad fugit praesentium. Voluptate doloremque consequatur autem sit ducimus est ipsum minus.\n\nEt eveniet enim dolores omnis sit blanditiis ea. Consequatur aut enim aperiam deleniti ducimus.\n\nAperiam est aliquid eum sunt possimus blanditiis hic. Voluptas aliquam non fuga commodi. Quibusdam facilis molestiae illum similique esse enim. Aut error quam est quas.', 'published', '2026-03-05 08:35:35', '2025-05-25 21:04:16', NULL, 131, NULL),
                                                                                                                                                           (18, 'Quia similique ut aut', 'quia-similique-ut-aut', 'Iusto pariatur qui et doloremque cum. Id omnis sint molestiae aspernatur facilis est vel eveniet. Facere nisi accusantium recusandae.\n\nCorporis quod eum nihil debitis et minima maiores. Tenetur enim laboriosam beatae. Distinctio ad quod consequatur labore non et dolor. Qui autem non dolores.\n\nEx nisi cumque odit provident odit qui. Itaque aperiam ex vero et et voluptas. Molestiae voluptate voluptatem qui ut.', 'published', '2026-03-05 08:35:35', '2025-07-08 03:05:31', NULL, 134, NULL),
                                                                                                                                                           (19, 'Maxime ab ad ratione molestiae eos', 'maxime-ab-ad-ratione-molestiae-eos', 'Placeat autem eos cupiditate aspernatur non numquam blanditiis nesciunt. Sequi inventore qui officia expedita ut adipisci. Molestiae voluptatem necessitatibus sed velit id in.\n\nAutem et dicta quasi omnis illum fuga. Non cum tempore non et a. Reprehenderit itaque delectus qui ullam eligendi dignissimos omnis. Eos optio modi quia id.\n\nIste ipsam hic occaecati minus aspernatur voluptatem. Nobis natus quo aspernatur voluptatibus. Voluptatem voluptas corporis voluptatum quo ut doloribus ut. Eligendi necessitatibus et odio aut sit et.', 'draft', '2026-03-05 08:35:35', '2025-09-07 07:12:44', NULL, 131, NULL),
                                                                                                                                                           (20, 'Voluptas id alias voluptas eius repudiandae illum', 'voluptas-id-alias-voluptas-eius-repudiandae-illum', 'Doloremque id odit et quidem veritatis non eveniet. Optio aperiam sunt voluptate sequi repellendus. Qui repudiandae facilis similique accusamus earum veritatis. Vero voluptatem rerum vel id placeat voluptates voluptatem.\n\nAccusantium fugit nesciunt aut sit rerum. Eius cum et dolores aut officiis iste. Dicta laborum natus atque magnam nemo.\n\nHarum earum et architecto in enim sunt eligendi. Molestiae molestiae culpa cupiditate dolorem id eos. Ipsum facilis vitae dolores accusamus eaque nisi ut dolorem. Earum consequatur numquam voluptas sed consequuntur iure est.', 'draft', '2026-03-05 08:35:35', '2025-06-20 11:07:00', NULL, 131, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `formation_user`
--

DROP TABLE IF EXISTS `formation_user`;
CREATE TABLE `formation_user` (
                                  `formation_id` int(10) UNSIGNED NOT NULL,
                                  `user_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `formation_user`
--

INSERT INTO `formation_user` (`formation_id`, `user_id`) VALUES
                                                             (11, 131),
                                                             (11, 132),
                                                             (11, 133),
                                                             (12, 131),
                                                             (12, 132),
                                                             (13, 131),
                                                             (13, 132),
                                                             (13, 133),
                                                             (14, 131),
                                                             (15, 131),
                                                             (15, 132),
                                                             (16, 131),
                                                             (16, 132),
                                                             (17, 131),
                                                             (17, 132),
                                                             (18, 131),
                                                             (18, 132),
                                                             (18, 133);

-- --------------------------------------------------------

--
-- Structure de la table `inscription`
--

DROP TABLE IF EXISTS `inscription`;
CREATE TABLE `inscription` (
                               `id` int(10) UNSIGNED NOT NULL,
                               `nom` varchar(100) NOT NULL,
                               `prenom` varchar(100) NOT NULL,
                               `email` varchar(180) NOT NULL,
                               `message` longtext DEFAULT NULL,
                               `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                               `treat` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
                               `treat_at` datetime DEFAULT NULL,
                               `formation_id` int(10) UNSIGNED NOT NULL,
                               `treat_by_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `inscription`
--

INSERT INTO `inscription` (`id`, `nom`, `prenom`, `email`, `message`, `created_at`, `treat`, `treat_at`, `formation_id`, `treat_by_id`) VALUES
                                                                                                                                            (1, 'Verhoeven', 'Febe', 'rania.desmedt@example.com', 'Ipsum explicabo quibusdam in vero quasi eos. Et dolores ut quidem quis officia. Quam est perferendis sed eos deleniti eius. Neque et at dolor sit exercitationem porro aut libero.', '2026-03-05 08:35:37', 1, '2026-02-05 08:35:37', 11, NULL),
                                                                                                                                            (2, 'De Vos', 'Marion', 'lea25@example.net', 'Accusantium aut iusto nam molestiae doloremque sit explicabo. Nostrum quis repellendus illum. Voluptatibus omnis sit aut quia sapiente cum. Non voluptatem sed ut delectus velit at vitae. Nisi repudiandae aut quisquam praesentium fugiat maiores sed.', '2026-03-05 08:35:37', 1, '2025-12-18 08:35:37', 11, NULL),
                                                                                                                                            (3, 'Nijs', 'Clément', 'chiara87@example.net', 'Voluptate ut qui quaerat laudantium. Beatae incidunt sit repellendus ut praesentium voluptas fuga voluptatem. Recusandae omnis asperiores maiores officia et molestias.', '2026-03-05 08:35:37', 1, '2026-01-05 08:35:37', 11, NULL),
                                                                                                                                            (4, 'Saidi', 'Alexis', 'david.raes@example.net', 'Veritatis quia id quos. Rerum debitis aut quas sed et et nisi. Illum et exercitationem placeat. Quia dolor et et consequatur quasi quibusdam consequuntur.', '2026-03-05 08:35:37', 1, '2026-01-20 08:35:37', 11, NULL),
                                                                                                                                            (5, 'Lecocq', 'Anaïs', 'alicia47@example.net', 'Optio et voluptate molestias ut et illum voluptas. Est soluta nesciunt error eligendi corporis deserunt nihil. Id asperiores beatae et autem facere.', '2026-03-05 08:35:37', 0, NULL, 11, NULL),
                                                                                                                                            (6, 'Rousseau', 'Thomas', 'hnijs@example.org', NULL, '2026-03-05 08:35:37', 0, NULL, 11, NULL),
                                                                                                                                            (7, 'Parmentier', 'Nora', 'deridder.hamza@example.org', NULL, '2026-03-05 08:35:37', 0, NULL, 11, NULL),
                                                                                                                                            (8, 'Verhaegen', 'Inès', 'margot11@example.com', NULL, '2026-03-05 08:35:37', 1, '2026-02-01 08:35:37', 12, NULL),
                                                                                                                                            (9, 'Lenaerts', 'Manon', 'klecomte@example.org', 'Voluptatem laborum eum repellendus voluptas porro voluptatem nostrum. Quo voluptas repellat et. A qui cumque beatae aperiam possimus architecto beatae. Fuga rem consequatur eum non accusantium est. Sed quo molestias voluptatem quasi.', '2026-03-05 08:35:37', 1, '2026-01-02 08:35:37', 12, NULL),
                                                                                                                                            (10, 'Louis', 'Senne', 'alexis.vanacker@example.org', 'Labore enim sint natus harum. Velit veniam rerum quia. Ut vero quo dolor quo. Aut minima consequatur officia error pariatur.', '2026-03-05 08:35:37', 1, '2026-01-30 08:35:37', 12, NULL),
                                                                                                                                            (11, 'Smets', 'Florian', 'lise.lambrechts@example.com', 'Harum ea possimus dolorem quisquam molestiae quas. Autem doloribus eos facere velit rem et. Fuga quia qui eum odio vitae consectetur ea consequatur. Iure et nobis non repellat corporis a et.', '2026-03-05 08:35:37', 0, NULL, 12, NULL),
                                                                                                                                            (12, 'Kaya', 'Benjamin', 'kvandenbroeck@example.com', 'Error eum veritatis exercitationem enim. Sunt quia maxime praesentium ipsum qui dolorum et. Vel ut id rerum et non. Sapiente ex sit in esse quisquam aut ut dolores.', '2026-03-05 08:35:37', 0, NULL, 12, NULL),
                                                                                                                                            (13, 'Lambrechts', 'Quinten', 'lverlinden@example.net', NULL, '2026-03-05 08:35:37', 0, NULL, 12, NULL),
                                                                                                                                            (14, 'Petit', 'Clément', 'nicolas.dumont@example.org', 'Molestias laborum aut commodi sit sit quo exercitationem. Debitis accusantium sint alias sequi vel in. Numquam ut reiciendis reiciendis. Distinctio numquam consequatur aliquid necessitatibus mollitia.', '2026-03-05 08:35:37', 1, '2026-01-31 08:35:37', 13, NULL),
                                                                                                                                            (15, 'Servais', 'Siebe', 'sofia.lebrun@example.com', 'Perferendis accusamus minima perspiciatis magnam voluptate. Doloremque non aliquid repellendus. Libero totam dolorum consequatur nihil temporibus dolores. Quis quia perferendis excepturi quos est quos.', '2026-03-05 08:35:37', 0, NULL, 13, NULL),
                                                                                                                                            (16, 'Verbeeck', 'Eline', 'baptiste.goossens@example.org', 'Et commodi aliquam quod commodi sit nihil aut. Ab nisi laborum distinctio ut. Minima voluptas soluta nihil impedit iste quod.', '2026-03-05 08:35:37', 0, NULL, 13, NULL),
                                                                                                                                            (17, 'Timmermans', 'Marie', 'nicolas37@example.net', 'Sit qui quo sunt beatae nisi ipsa. Molestiae sapiente placeat eum temporibus voluptas. Dignissimos atque aliquid ipsa error et quam. Id sit nisi alias rem cupiditate.', '2026-03-05 08:35:37', 1, '2025-12-25 08:35:37', 14, NULL),
                                                                                                                                            (18, 'Simon', 'Robbe', 'arne41@example.com', NULL, '2026-03-05 08:35:37', 0, NULL, 14, NULL),
                                                                                                                                            (19, 'Roland', 'Matteo', 'ldeclercq@example.org', NULL, '2026-03-05 08:35:37', 0, NULL, 14, NULL),
                                                                                                                                            (20, 'Michiels', 'Léa', 'heymans.ethan@example.org', NULL, '2026-03-05 08:35:37', 1, '2026-02-21 08:35:37', 15, NULL),
                                                                                                                                            (21, 'Leclercq', 'Léa', 'hardy.adam@example.com', NULL, '2026-03-05 08:35:37', 0, NULL, 15, NULL),
                                                                                                                                            (22, 'Benali', 'Tuur', 'diego.michel@example.net', 'Minima earum et sunt qui eveniet deserunt libero eligendi. Unde odio omnis aliquam quo. In voluptates voluptas soluta aliquid quos eum. Soluta pariatur magnam molestiae eaque cumque.', '2026-03-05 08:35:37', 0, NULL, 15, NULL),
                                                                                                                                            (23, 'Cornelis', 'Célia', 'osaidi@example.com', 'Sint error ut autem ea cum ipsa. Praesentium voluptatem et quia voluptate. Eos veritatis eligendi quis quam. Optio alias nihil dicta necessitatibus. Ut enim molestiae aut quos optio dicta.', '2026-03-05 08:35:37', 1, '2025-12-25 08:35:37', 16, NULL),
                                                                                                                                            (24, 'Bernard', 'Helena', 'lemmens.eloise@example.org', 'Voluptatum a voluptatibus qui est fugit. Rerum quod voluptas optio labore. Aliquid rerum assumenda et minima.', '2026-03-05 08:35:37', 1, '2025-12-05 08:35:37', 16, NULL),
                                                                                                                                            (25, 'Herman', 'Emma', 'niels.desmedt@example.com', NULL, '2026-03-05 08:35:37', 1, '2026-01-09 08:35:37', 16, NULL),
                                                                                                                                            (26, 'Vervoort', 'Julia', 'celik.elise@example.com', NULL, '2026-03-05 08:35:37', 0, NULL, 16, NULL),
                                                                                                                                            (27, 'Lambrechts', 'Linde', 'leon72@example.com', 'Est sunt vel culpa quasi. Aut numquam ratione unde libero consectetur. Sequi voluptas facere et dicta.', '2026-03-05 08:35:37', 0, NULL, 16, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
CREATE TABLE `messenger_messages` (
                                      `id` bigint(20) NOT NULL,
                                      `body` longtext NOT NULL,
                                      `headers` longtext NOT NULL,
                                      `queue_name` varchar(190) NOT NULL,
                                      `created_at` datetime NOT NULL,
                                      `available_at` datetime NOT NULL,
                                      `delivered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `page`
--

DROP TABLE IF EXISTS `page`;
CREATE TABLE `page` (
                        `id` int(10) UNSIGNED NOT NULL,
                        `title` varchar(255) NOT NULL,
                        `slug` varchar(255) NOT NULL,
                        `content` longtext NOT NULL,
                        `status` varchar(20) NOT NULL DEFAULT 'draft',
                        `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                        `published_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `page`
--

INSERT INTO `page` (`id`, `title`, `slug`, `content`, `status`, `created_at`, `published_at`) VALUES
                                                                                                  (7, 'Accueil', 'accueil', '<p>Cum non atque inventore quia illum molestias illum. Dolorem voluptatem nesciunt esse placeat et ut beatae eveniet. Odit quia et sed. Quaerat eaque cum earum et debitis ut consequatur corporis.</p><p>Qui voluptas rem quo et. Dignissimos non sed cumque veniam ipsum. Excepturi et sed ut consequatur laudantium incidunt officiis unde. Sit sint quam et est.</p><p>Quae porro est est rerum sit. Cupiditate voluptas enim quis. Atque maiores sed sit consequatur voluptatum.</p>', 'published', '2026-03-05 08:35:35', '2026-02-05 08:35:35'),
                                                                                                  (8, 'À propos', 'a-propos', '<p>Ut accusantium dolore magni dolor autem. Quam eos error eaque molestiae atque perspiciatis. Optio cumque consequatur accusamus tempore. Et sed id dolorum neque est non omnis. Velit enim iusto non est.</p><p>Aut velit et sit cupiditate quos sed. Est maiores voluptatem porro laborum quia beatae iure alias. Nihil beatae consequatur neque dolore fugiat fugiat sit.</p><p>Quasi quia aliquam eum magnam omnis in nihil. Dolore quidem accusantium eaque. Velit et consequuntur consequatur ea voluptate.</p>', 'published', '2026-03-05 08:35:35', '2026-01-05 08:35:35'),
                                                                                                  (9, 'Contact', 'contact', '<p>Rem rerum vero cum eum dolor. Numquam nisi magni ipsum corrupti. Asperiores similique quo culpa vitae rerum laudantium dolores. Ut placeat maiores nulla quae quaerat velit.</p><p>Ut tempore doloremque labore natus. Et qui eos quidem et deleniti. Expedita eum dolorem quo.</p><p>Ut non in aut nulla. Velit vel et rerum sint saepe. Maxime omnis sit provident sed dolore totam sunt. Nesciunt quidem rerum non numquam maiores.</p>', 'draft', '2026-03-05 08:35:35', '2025-06-26 01:39:41'),
                                                                                                  (10, 'Vero eveniet et', 'vero-eveniet-et', '<p>Aperiam amet mollitia corrupti sit est nulla ut. Laudantium necessitatibus qui commodi voluptatem. Et dolor doloribus consequatur repellendus quibusdam quibusdam ex praesentium.</p><p>Alias praesentium provident natus explicabo neque voluptatem dolorum. Repudiandae ea sunt eveniet officia.</p><p>Corrupti ut incidunt ea distinctio officiis. Laboriosam minima incidunt enim doloremque dolorem est impedit. In qui dolorem expedita accusantium minima et. Placeat et inventore beatae autem sequi possimus modi totam.</p>', 'draft', '2026-03-05 08:35:35', '2025-08-02 09:33:08'),
                                                                                                  (11, 'Error et libero', 'error-et-libero', '<p>Dolorem ex harum eveniet et adipisci nam illum. Voluptas non odio occaecati quam cumque aut. Molestias quis eum nisi.</p><p>Voluptatibus occaecati voluptatibus tempora excepturi. Molestiae voluptatem numquam fugit dolor vero sit. Quaerat aut quidem dolor aut odio. Ab perferendis dolores dolore odit enim nemo sit.</p><p>Perferendis autem ad eligendi pariatur illum nemo. Nihil nostrum quia qui quo odit commodi. Nisi voluptatem earum voluptatem aut.</p>', 'published', '2026-03-05 08:35:35', '2025-09-03 02:56:03'),
                                                                                                  (12, 'Velit perspiciatis enim', 'velit-perspiciatis-enim', '<p>Dicta voluptas dolorum debitis ut sit accusamus. Vero quas nihil rerum in ratione molestiae mollitia. In fuga nemo laborum unde.</p><p>Deleniti neque animi magnam. Eos dolores ea aperiam sit omnis nam. Vitae enim adipisci et earum quis. Occaecati et magni ullam quod omnis id esse sit.</p><p>Aut tenetur qui laudantium. Facilis deserunt qui magni. Sunt nostrum est debitis nobis.</p>', 'published', '2026-03-05 08:35:35', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `page_user`
--

DROP TABLE IF EXISTS `page_user`;
CREATE TABLE `page_user` (
                             `page_id` int(10) UNSIGNED NOT NULL,
                             `user_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `partenaire`
--

DROP TABLE IF EXISTS `partenaire`;
CREATE TABLE `partenaire` (
                              `id` int(10) UNSIGNED NOT NULL,
                              `nom` varchar(255) NOT NULL,
                              `description` longtext DEFAULT NULL,
                              `logo` varchar(255) DEFAULT NULL,
                              `url` varchar(255) DEFAULT NULL,
                              `is_active` tinyint(3) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `partenaire`
--

INSERT INTO `partenaire` (`id`, `nom`, `description`, `logo`, `url`, `is_active`) VALUES
                                                                                      (21, 'Yildirim', 'Modi ullam impedit amet deserunt. Et autem consequuntur necessitatibus perferendis similique sit sunt. Ipsa velit repellendus fugiat est aliquam quo.', NULL, 'http://www.marechal.be/', 1),
                                                                                      (22, 'Bastin SCS', 'Velit dignissimos rerum itaque at ut atque. Possimus esse tempore sed consectetur inventore inventore qui. Libero rem hic ratione amet amet dolorem.', NULL, 'https://pierre.be/error-est-sed-pariatur-ducimus-molestiae.html', 1),
                                                                                      (23, 'Louis', 'Cupiditate qui doloremque et harum ducimus architecto. Nesciunt explicabo est ut porro doloribus. Cum omnis quod aspernatur quam.', NULL, 'http://mertens.net/repudiandae-nemo-et-voluptatum-et-nihil-error-repellat.html', 1),
                                                                                      (24, 'Michiels', 'Quibusdam qui quae nobis. Facere veniam deserunt dolore impedit quod. Consequuntur quis in assumenda. Quo esse nihil libero similique odit esse ipsum.', NULL, 'http://claessens.org/', 1),
                                                                                      (25, 'Heymans', 'Incidunt a asperiores quam. Sequi libero delectus ad qui totam unde at. Accusamus ut ipsa accusamus error totam. Inventore quia maiores a nobis.', NULL, 'http://www.hendrickx.com/sit-consequatur-magnam-et-non', 1),
                                                                                      (26, 'Amrani ASBL', 'Fugit ipsum et iure vitae veritatis. Vel dolorum et ut quo. Et laudantium nam optio maxime similique nesciunt. Dolores in illum non sunt tenetur.', NULL, 'http://www.gilles.be/cupiditate-aut-asperiores-labore-vero-molestiae-illum-eum', 1),
                                                                                      (27, 'Sahin Associations', 'Eum tempore excepturi voluptatum. Dolores dolorem voluptate et tempore non. Quo eligendi quo omnis aut quasi.', NULL, 'http://carlier.net/qui-eos-rem-tempora-autem-repellendus-sit-quas', 0),
                                                                                      (28, 'De Coster SCS', 'Eum fuga ea est amet aut consequatur voluptates vel. Sed non voluptatum nihil tempora eos aut. Sapiente laboriosam voluptatibus quia harum. Dolorum nihil debitis ratione in.', NULL, 'http://vanhecke.org/enim-sequi-et-doloremque-voluptatem-odit-quis', 0);

-- --------------------------------------------------------

--
-- Structure de la table `rating`
--

DROP TABLE IF EXISTS `rating`;
CREATE TABLE `rating` (
                          `id` int(10) UNSIGNED NOT NULL,
                          `value` smallint(5) UNSIGNED NOT NULL,
                          `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                          `user_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `rating`
--

INSERT INTO `rating` (`id`, `value`, `created_at`, `user_id`) VALUES
                                                                  (1, 3, '2026-03-05 08:35:36', 148),
                                                                  (2, 2, '2026-03-05 08:35:36', 139),
                                                                  (3, 3, '2026-03-05 08:35:36', 152),
                                                                  (4, 5, '2026-03-05 08:35:36', 141),
                                                                  (5, 3, '2026-03-05 08:35:36', 140),
                                                                  (6, 3, '2026-03-05 08:35:36', 148),
                                                                  (7, 2, '2026-03-05 08:35:36', 145),
                                                                  (8, 2, '2026-03-05 08:35:36', 145),
                                                                  (9, 1, '2026-03-05 08:35:36', 155),
                                                                  (10, 1, '2026-03-05 08:35:36', 149),
                                                                  (11, 5, '2026-03-05 08:35:36', 146),
                                                                  (12, 5, '2026-03-05 08:35:36', 158),
                                                                  (13, 3, '2026-03-05 08:35:36', 159),
                                                                  (14, 1, '2026-03-05 08:35:36', 150),
                                                                  (15, 5, '2026-03-05 08:35:36', 136),
                                                                  (16, 2, '2026-03-05 08:35:36', 158),
                                                                  (17, 5, '2026-03-05 08:35:36', 154),
                                                                  (18, 2, '2026-03-05 08:35:36', 160),
                                                                  (19, 5, '2026-03-05 08:35:36', 155),
                                                                  (20, 4, '2026-03-05 08:35:36', 138),
                                                                  (21, 1, '2026-03-05 08:35:36', 150),
                                                                  (22, 2, '2026-03-05 08:35:36', 137),
                                                                  (23, 2, '2026-03-05 08:35:36', 160),
                                                                  (24, 3, '2026-03-05 08:35:36', 155),
                                                                  (25, 3, '2026-03-05 08:35:36', 148),
                                                                  (26, 1, '2026-03-05 08:35:36', 141),
                                                                  (27, 5, '2026-03-05 08:35:36', 153),
                                                                  (28, 4, '2026-03-05 08:35:36', 141),
                                                                  (29, 3, '2026-03-05 08:35:36', 136),
                                                                  (30, 4, '2026-03-05 08:35:36', 136),
                                                                  (31, 1, '2026-03-05 08:35:36', 140),
                                                                  (32, 5, '2026-03-05 08:35:36', 153),
                                                                  (33, 4, '2026-03-05 08:35:36', 148),
                                                                  (34, 2, '2026-03-05 08:35:36', 156),
                                                                  (35, 5, '2026-03-05 08:35:36', 158),
                                                                  (36, 3, '2026-03-05 08:35:36', 142),
                                                                  (37, 2, '2026-03-05 08:35:36', 154),
                                                                  (38, 3, '2026-03-05 08:35:36', 140),
                                                                  (39, 2, '2026-03-05 08:35:36', 143),
                                                                  (40, 2, '2026-03-05 08:35:36', 155),
                                                                  (41, 5, '2026-03-05 08:35:36', 152),
                                                                  (42, 3, '2026-03-05 08:35:36', 149),
                                                                  (43, 4, '2026-03-05 08:35:37', 153),
                                                                  (44, 5, '2026-03-05 08:35:37', 146),
                                                                  (45, 2, '2026-03-05 08:35:37', 148);

-- --------------------------------------------------------

--
-- Structure de la table `rating_works`
--

DROP TABLE IF EXISTS `rating_works`;
CREATE TABLE `rating_works` (
                                `rating_id` int(10) UNSIGNED NOT NULL,
                                `works_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `rating_works`
--

INSERT INTO `rating_works` (`rating_id`, `works_id`) VALUES
                                                         (1, 1),
                                                         (2, 1),
                                                         (3, 1),
                                                         (4, 1),
                                                         (5, 1),
                                                         (6, 2),
                                                         (7, 3),
                                                         (8, 3),
                                                         (9, 3),
                                                         (10, 3),
                                                         (11, 4),
                                                         (12, 4),
                                                         (13, 4),
                                                         (14, 4),
                                                         (15, 4),
                                                         (16, 5),
                                                         (17, 5),
                                                         (18, 5),
                                                         (19, 5),
                                                         (20, 5),
                                                         (21, 6),
                                                         (22, 7),
                                                         (23, 7),
                                                         (24, 7),
                                                         (25, 7),
                                                         (26, 8),
                                                         (27, 8),
                                                         (28, 8),
                                                         (29, 9),
                                                         (30, 9),
                                                         (31, 10),
                                                         (32, 10),
                                                         (33, 11),
                                                         (34, 11),
                                                         (35, 12),
                                                         (36, 13),
                                                         (37, 13),
                                                         (38, 13),
                                                         (39, 13),
                                                         (40, 14),
                                                         (41, 14),
                                                         (42, 15),
                                                         (43, 15),
                                                         (44, 15),
                                                         (45, 15);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
                        `id` int(10) UNSIGNED NOT NULL,
                        `email` varchar(180) NOT NULL,
                        `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`roles`)),
                        `password` varchar(255) NOT NULL,
                        `user_name` varchar(50) NOT NULL,
                        `activation_token` varchar(64) DEFAULT NULL,
                        `status` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
                        `reset_password_token` varchar(64) DEFAULT NULL,
                        `reset_password_requested_at` datetime DEFAULT NULL,
                        `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                        `avatar_name` varchar(255) DEFAULT NULL,
                        `biography` varchar(600) DEFAULT NULL,
                        `external_link1` varchar(255) DEFAULT NULL,
                        `external_link2` varchar(255) DEFAULT NULL,
                        `external_link3` varchar(255) DEFAULT NULL,
                        `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `user_name`, `activation_token`, `status`, `reset_password_token`, `reset_password_requested_at`, `created_at`, `avatar_name`, `biography`, `external_link1`, `external_link2`, `external_link3`, `updated_at`) VALUES
                                                                                                                                                                                                                                                                            (129, 'lander80@example.net', '[\"ROLE_ADMIN\"]', '$2y$13$wl6mHwBVVmTmegJf/S/KnubKn1isF8jvl2zR84srbrkpVHDsCOmN2', 'aPQZW7dpWIkC', NULL, 1, NULL, NULL, '2026-03-05 08:35:23', NULL, 'Totam deleniti libero laboriosam deleniti ut doloremque. Quidem id minus quod sunt sunt aut. Ut officiis consequatur unde ut sunt aut. Ut quas veniam dolorem dolore nihil laborum.', NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                            (130, 'salma38@example.org', '[\"ROLE_ADMIN\"]', '$2y$13$mPqkNQ6Oo4ckwk.30pTZhuFuu67B3atu5TV3Mwdal25GUHOCxEPTu', 'ZxKrgN6xImf', NULL, 1, NULL, NULL, '2026-03-05 08:35:24', NULL, 'Et consequatur dolore inventore quaerat non sunt. Quam velit quod accusantium labore aut. Qui mollitia fugit aliquid qui quo voluptatum dolor. Ratione aut laudantium omnis nemo est molestiae.', NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                            (131, 'robert.ferre@example.net', '[\"ROLE_FORMATEUR\"]', '$2y$13$Jjjx4HDIl8yzcYBlUB4e5eqYT9z2SsgyLo72VSmbhcyuZK9YFIAhG', 'DPBV89oM3FiPzEw', NULL, 1, NULL, NULL, '2026-03-05 08:35:24', NULL, NULL, NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                            (132, 'coppens.baptiste@example.com', '[\"ROLE_FORMATEUR\"]', '$2y$13$b9jk1gHDI2.l3nZnX2PZ4eIUc7wLa4kXLpijuMIszH1MpeWgeRgMe', 'SGw5C4ZmpWS', NULL, 1, NULL, NULL, '2026-03-05 08:35:24', NULL, NULL, NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                            (133, 'qbenali@example.net', '[\"ROLE_FORMATEUR\"]', '$2y$13$il.TbfrQGNg10gkEMWJfquA.wKvxf2eYFQ1CCDTOBd3UBKrpJoLkW', 'JEjyPPaKIiRT9nu', NULL, 1, NULL, NULL, '2026-03-05 08:35:25', NULL, NULL, NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                            (134, 'michiels.aya@example.org', '[\"ROLE_FORMATEUR\"]', '$2y$13$OjFL9.XkutGdyP0DFR65quUvZi0ZMFTVeWuwTibqFOvzR5jE2Kp12', 'bIE6tZDK2Z8N', NULL, 1, NULL, NULL, '2026-03-05 08:35:25', NULL, 'Sint at voluptas quibusdam et saepe vitae. Quo aut non et. Et enim sequi qui officia eaque similique molestias.', 'http://www.lecomte.net/quae-esse-quos-corporis-earum-in-delectus-sit-minima', NULL, NULL, NULL),
                                                                                                                                                                                                                                                                            (135, 'fmahieu@example.com', '[\"ROLE_FORMATEUR\"]', '$2y$13$wqU2bICS13IBfB7YPMCoOOquIpr9qAZwKgQNsOkMKK2otZC4Ahj0e', 'MM_NyQCgiUKS', NULL, 1, NULL, NULL, '2026-03-05 08:35:25', NULL, NULL, NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                            (136, 'rjacques@example.com', '[]', '$2y$13$Ve9E1Lsfd78L22bfXW4.oenEROR.xEm75DKS9bLilUDmZ9CtXlv8u', 'oCdk5hMZsY', NULL, 1, NULL, NULL, '2026-03-05 08:35:26', NULL, 'Quo excepturi sunt fuga. Qui et eum qui vitae dicta. Ut totam veritatis omnis quae consequatur consequatur reiciendis omnis. Rerum omnis quis quasi non necessitatibus doloremque magni quia.', NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                            (137, 'milan85@example.org', '[]', '$2y$13$.9tXgub1e7jOBVzfv6V0SuZ4T/mzTpgpSSitlt73pA0YdOOw7JgMu', 'Jh7UVN_ed2WvT', NULL, 1, NULL, NULL, '2026-03-05 08:35:26', NULL, 'Totam facilis voluptas natus sed dignissimos quia quis. Facilis sequi sed voluptatem magnam. Vitae cum ab sed voluptatibus.', NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                            (138, 'seppe22@example.org', '[]', '$2y$13$/Esz3eRKr2JeFuK0vxdCt.C9C3WpGEv7B4onuzhMobRspJ5xk9c8O', 'fkfbQp9xbuelF', NULL, 1, NULL, NULL, '2026-03-05 08:35:27', NULL, 'Voluptatem enim repellendus aliquid dolorem. Quibusdam sit modi consectetur et id ullam. Quod odit exercitationem sint repudiandae qui. Et rem deleniti ab quae ut.', 'http://urbain.be/est-quis-ea-voluptatum-ipsam-quam-veritatis-recusandae.html', NULL, NULL, NULL),
                                                                                                                                                                                                                                                                            (139, 'qdemir@example.org', '[]', '$2y$13$EUTGoOX6XmliqzwxztCdi.8F/PN6xfm1p0HIK6aw89Tr9utt.qsKS', 'JqNhslf', NULL, 1, NULL, NULL, '2026-03-05 08:35:27', NULL, NULL, 'http://www.meunier.be/', NULL, NULL, NULL),
                                                                                                                                                                                                                                                                            (140, 'laura86@example.com', '[]', '$2y$13$xGPqsnKcCz3LggKtIuUM6Oxfo60oLMnA/Kc5XnWux8XsO2DGIDGsC', 'dqndpzfpe', NULL, 1, NULL, NULL, '2026-03-05 08:35:27', NULL, 'Exercitationem voluptatem voluptas enim delectus commodi aut. Quae aut soluta deserunt et. Quo nesciunt debitis aperiam culpa molestiae rerum cumque corporis.', 'http://christiaens.net/temporibus-facilis-magnam-voluptatem-sequi-facere-explicabo.html', NULL, NULL, NULL),
                                                                                                                                                                                                                                                                            (141, 'saidi.alicia@example.com', '[]', '$2y$13$1oHBMVvhgefKG/3TAwTkbewzHQOsWfQ1X70CfX4MqlVQdpQrgrc7O', 'EwkANN8jisI6b', NULL, 1, NULL, NULL, '2026-03-05 08:35:28', NULL, 'Impedit asperiores error aut. Perferendis dolore dolores expedita est ex. Possimus consequuntur vel iure occaecati velit sequi id. Magni aut dolor aut voluptatem non et.', NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                            (142, 'lina.gilson@example.com', '[]', '$2y$13$6vV./HbsObzK0giZ9WPSrOBNIuYtNue4ai7ssKqFpWYCsTDwftbdG', 'AXkFHgB', NULL, 1, NULL, NULL, '2026-03-05 08:35:28', NULL, NULL, NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                            (143, 'vandamme.nolan@example.com', '[]', '$2y$13$MKID3/8ceX7MBfthVsxfz.rw0s9dIif.s8rJSiI9RrBRFQ5JuYPsC', 'XKnKOTUd', NULL, 1, NULL, NULL, '2026-03-05 08:35:28', NULL, NULL, NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                            (144, 'yverhaeghe@example.com', '[]', '$2y$13$4xiEedmrY.FBgEc9s3.MtOEv0szEviO6nSPQQ0haqniuPcOD26ZCO', 'HYxmXJOG', NULL, 1, NULL, NULL, '2026-03-05 08:35:29', NULL, NULL, 'http://www.luyten.net/ipsam-est-optio-nostrum-dolores-facere-doloribus-totam.html', NULL, NULL, NULL),
                                                                                                                                                                                                                                                                            (145, 'dlenaerts@example.org', '[]', '$2y$13$HYH3vizGapY3Og26Es4BIe1N6myFoUL8YhG1NZMYdFCtDbBPC8Q72', 'p6q3W0H05zZ', NULL, 1, NULL, NULL, '2026-03-05 08:35:29', NULL, 'Voluptas non culpa inventore iure occaecati quod. Veniam omnis libero quo ea laborum quidem. Deserunt velit officia amet nam.', NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                            (146, 'maxim51@example.org', '[]', '$2y$13$5pSlybyzo52aPHLsxqjPkuMYJJmAQc4GCyMC1NKZWmSvFiDicKI3S', 'wdMCg5RvHuW8', NULL, 1, NULL, NULL, '2026-03-05 08:35:30', NULL, NULL, NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                            (147, 'diego.vanacker@example.org', '[]', '$2y$13$LoeEyjxGNSISJrjtIQ.5muKN53alDsscj3SJRuct3X3b7ga5/2x0C', 'mLo8KWjnfiK', NULL, 1, NULL, NULL, '2026-03-05 08:35:30', NULL, NULL, NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                            (148, 'srousseau@example.net', '[]', '$2y$13$TmVuOjMR9ASFoMSUsrdva.JTr.p2SFqGHmZ4uNhEc6bAEbu7NR8lW', 'fE9phxh9sRlpM5', NULL, 1, NULL, NULL, '2026-03-05 08:35:30', NULL, 'Asperiores repellendus et consequatur quae. Laboriosam asperiores ut dolorem est. Dignissimos nihil rerum nihil alias fugiat.', NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                            (149, 'declercq.aya@example.net', '[]', '$2y$13$XCPV/pYj7sRZiWhTv57U8uh66H4IdyBHAsv.tdwiGdgcMwU0.doly', 'BVDJ0u4W6', NULL, 1, NULL, NULL, '2026-03-05 08:35:31', NULL, 'Sunt magnam excepturi dicta nam ea est omnis. Optio vitae ratione eum sed. Illo placeat soluta alias.', NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                            (150, 'eva15@example.net', '[]', '$2y$13$hjVSygNNTsR44hz.A5bwEuShG86qHZbqwG6p9exvqoioDOrNgXzca', 'r3ODEwEshk9O', NULL, 1, NULL, NULL, '2026-03-05 08:35:31', NULL, 'Dolore esse nihil alias excepturi ut cupiditate aut. Quidem reiciendis accusantium velit atque. Quis sunt est quas et culpa in.', NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                            (151, 'lina.dubois@example.net', '[]', '$2y$13$WCFN69f1ZWy/yhp5C0mQPeWJZNdvkQGGaXXLXsbrgwNmMwAiEFCC6', 'dsCsW8S', NULL, 1, NULL, NULL, '2026-03-05 08:35:32', NULL, 'Quis eos voluptatem enim. Nesciunt quis quis dolor est saepe quasi.', NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                            (152, 'samuel06@example.org', '[]', '$2y$13$OyhCMdAn1Z58OH9Dcj/8E.A/bTclYiXxmrKCzvKUSrNwsXzu9wKFm', 'WGDhtTdpYZAU6d', NULL, 1, NULL, NULL, '2026-03-05 08:35:32', NULL, 'Pariatur dolore voluptatem molestiae sunt nihil pariatur officiis. Modi dolores nisi totam deleniti. Ea cumque placeat est mollitia.', 'http://masson.net/qui-magni-voluptates-doloremque-nam-laborum-inventore-rerum-voluptatem', NULL, NULL, NULL),
                                                                                                                                                                                                                                                                            (153, 'psahin@example.net', '[]', '$2y$13$0hSzpFSx8fF2i0fxIxQD.eiyNpWMLgp0WXba5.sVAOJuv4jg8FI0m', 'zMjV65', NULL, 1, NULL, NULL, '2026-03-05 08:35:32', NULL, 'Rerum ex doloribus sit consequatur assumenda magnam sed. Animi delectus non vero iste. Reprehenderit porro et sequi et vitae libero sit.', NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                            (154, 'hchristiaens@example.com', '[]', '$2y$13$30EtU143dLB5MHqFD2i82eTU/xyq5wJBWOg1rwIUuv3Ox6HJYi4z.', 'bAMVuf_Rj', NULL, 1, NULL, NULL, '2026-03-05 08:35:33', NULL, 'Doloribus et quasi qui quidem deleniti amet amet tempore. Modi fugit modi id quia beatae laborum aut. Doloribus similique fuga pariatur quae.', 'http://poncelet.be/ut-repudiandae-assumenda-ipsum-laboriosam-id-laborum.html', NULL, NULL, NULL),
                                                                                                                                                                                                                                                                            (155, 'aaron.desmet@example.com', '[]', '$2y$13$IRJKT2JvepxAMk.gnyrANe6muAqhgSfoJoYPWA8rIpUXyokTeJ/aO', 'w5mXS2', NULL, 1, NULL, NULL, '2026-03-05 08:35:33', NULL, 'Qui non voluptatem quasi vel explicabo et. Qui ullam voluptas maiores qui voluptatem veniam sed. Sed laborum beatae ducimus perferendis consectetur est. Deserunt ut qui architecto aliquid dolorum vel.', NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                            (156, 'hugo.claes@example.org', '[]', '$2y$13$O9gEIIkTtND9KIdtfpJrgeJ3Y2Yze4NWkP1q9N6zl50.nXSIbNLUG', 'z4xx9TTo4', NULL, 1, NULL, NULL, '2026-03-05 08:35:33', NULL, NULL, NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                            (157, 'liam84@example.org', '[]', '$2y$13$YqVbx/DbZwne4vcbCo2N6uKNTywBjgOA6pgQHNNBEaGA8tocAFJcC', 'NaSGwsI4xT', NULL, 1, NULL, NULL, '2026-03-05 08:35:34', NULL, 'Et odit inventore rerum at ut voluptatibus. Nemo aliquam qui id et. Aut blanditiis libero corrupti atque. Cum omnis fugit ut eaque perspiciatis tempora.', NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                            (158, 'ehardy@example.org', '[]', '$2y$13$9rpz/eLSZlSbi9B35LFz0.8HSO18qnWPrZJ7j1/w7jDfQ5SeSt2fu', 'qDh5LVMEf', NULL, 1, NULL, NULL, '2026-03-05 08:35:34', NULL, 'Accusantium sit aliquid fugit. Cumque non quibusdam harum ea officiis aliquam occaecati.', NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                            (159, 'clement.jansen@example.com', '[]', '$2y$13$WBR6bSiokD0twdz5nUeRuOhpwW9dsI8NWTrFAyRYm83qxe82TW/x6', 'x8IFAaInv15b44', NULL, 1, NULL, NULL, '2026-03-05 08:35:35', NULL, 'Optio veniam magni sit maxime. Id labore et aut cumque ut ut. Quam vel rem voluptatibus ex repellendus.', NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                            (160, 'amrani.lars@example.com', '[]', '$2y$13$st6CaWElA3YOMSCjRhXyOuECifvgjkikwnKz.vrDAZos84IzGvwHi', 'MPqESP', NULL, 1, NULL, NULL, '2026-03-05 08:35:35', NULL, 'Dignissimos et est perferendis et nobis. Repudiandae perspiciatis pariatur sit libero libero et rerum. Dolore cupiditate est at. Quod voluptatum aut voluptatibus nostrum qui voluptas tempore.', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `works`
--

DROP TABLE IF EXISTS `works`;
CREATE TABLE `works` (
                         `id` int(10) UNSIGNED NOT NULL,
                         `title` varchar(255) NOT NULL,
                         `slug` varchar(255) NOT NULL,
                         `description` longtext DEFAULT NULL,
                         `status` varchar(20) NOT NULL DEFAULT 'draft',
                         `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                         `published_at` datetime DEFAULT NULL,
                         `formation_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `works`
--

INSERT INTO `works` (`id`, `title`, `slug`, `description`, `status`, `created_at`, `published_at`, `formation_id`) VALUES
                                                                                                                       (1, 'Repellendus omnis quod quo pariatur sint', 'repellendus-omnis-quod-quo-pariatur-sint', 'Explicabo aliquam vitae consequatur at quis possimus. Quia enim sint nobis maxime quidem. Reiciendis iure animi voluptatem ut sed.\n\nQui sunt explicabo aut doloremque neque. Enim et qui omnis sit accusamus voluptatum explicabo. Et porro ut dignissimos reprehenderit quia. At a quis cum neque qui aliquid sed quo.', 'published', '2026-03-05 08:35:35', '2025-11-19 04:11:43', 11),
                                                                                                                       (2, 'Fugit tempore et ut quas', 'fugit-tempore-et-ut-quas', 'Et aut molestiae sed voluptas omnis voluptatibus impedit. Eaque dolorem qui soluta minima modi. Fugiat provident aperiam distinctio et vitae voluptatem. Amet molestiae et officia.\n\nDolor reprehenderit quis quidem tenetur aperiam. Maiores magni quia ea cupiditate. Labore suscipit animi non et quod.', 'published', '2026-03-05 08:35:35', '2025-12-11 14:30:00', 11),
                                                                                                                       (3, 'Vero doloremque delectus aut', 'vero-doloremque-delectus-aut', 'Facilis laudantium quis enim laboriosam qui qui recusandae architecto. Accusamus non id deserunt reiciendis consequatur. Suscipit minus ea ad officia voluptatem inventore. Totam at quisquam commodi rerum.\n\nQuasi est labore molestiae enim tenetur. Ea eos ut earum itaque et. Perspiciatis labore aperiam perferendis repudiandae possimus consectetur. Asperiores nobis doloribus qui sunt id est est.', 'published', '2026-03-05 08:35:35', NULL, 12),
                                                                                                                       (4, 'Voluptatibus maxime culpa', 'voluptatibus-maxime-culpa', 'Consequatur officia quibusdam quis veritatis. Exercitationem omnis nihil cum voluptates. Eveniet nemo repudiandae est enim.\n\nQui reiciendis eaque minima est nam nihil. Dolores ipsam error velit unde saepe qui et. Mollitia nobis reprehenderit accusantium illo. Ducimus est dolor amet praesentium.', 'published', '2026-03-05 08:35:35', '2025-09-30 13:09:42', 12),
                                                                                                                       (5, 'Laborum ipsa molestias quae magni', 'laborum-ipsa-molestias-quae-magni', 'Rem quae illum dolorem harum. Voluptatem hic pariatur ut quae accusamus. Soluta atque ipsum distinctio cupiditate. Autem tenetur quisquam molestiae dolore quasi quas est aut. Aut reprehenderit odit eos non aut.\n\nPariatur eveniet harum repudiandae accusantium non ducimus facere. Et voluptatem tenetur fuga quas adipisci qui. Itaque dolore veniam culpa voluptate.', 'published', '2026-03-05 08:35:35', NULL, 12),
                                                                                                                       (6, 'Animi et saepe quibusdam nulla', 'animi-et-saepe-quibusdam-nulla', 'Magni sint et saepe neque adipisci. Blanditiis dolores ex similique saepe fugit eum et. Non doloribus illum ut et nihil aut aspernatur.\n\nCorrupti incidunt temporibus illo quis quis atque eveniet. Aliquid totam quo dolore qui.', 'published', '2026-03-05 08:35:35', '2025-10-14 22:47:01', 12),
                                                                                                                       (7, 'Eos aperiam quo', 'eos-aperiam-quo', 'Quia autem fuga voluptas quisquam cumque quis. Voluptas sunt similique placeat nesciunt. Nulla ut ipsum quam animi ipsa id quia. Maiores sunt quisquam sed ipsam.\n\nNeque in vitae soluta aut consequatur. Aliquid exercitationem voluptate deserunt sit nisi modi. Aspernatur quos rerum sequi sed corrupti. Iste doloribus qui et sapiente illum.', 'published', '2026-03-05 08:35:35', NULL, 12),
                                                                                                                       (8, 'Iure qui corrupti non', 'iure-qui-corrupti-non', 'Quod distinctio nostrum ipsam omnis. Veniam tenetur mollitia excepturi ut. Aperiam quia eum dignissimos sequi in deserunt consequatur. Est tempora ab delectus facilis vel.\n\nDolor reprehenderit reprehenderit et impedit. Accusamus veniam ab nam porro voluptatibus. Accusamus consequatur et rem nulla debitis qui ratione quo. Dolor porro aut non atque eum quam.', 'published', '2026-03-05 08:35:35', '2025-07-28 04:25:14', 13),
                                                                                                                       (9, 'Et deserunt aut repellendus amet', 'et-deserunt-aut-repellendus-amet', 'Ad minus quia minus rerum. Eveniet deleniti quasi sed sed. Rerum quia doloremque fuga sunt quasi deleniti.\n\nAnimi fugiat enim sit. Repudiandae dignissimos dolorum enim ad. Fuga natus dolorem iusto repellendus nulla maiores.', 'published', '2026-03-05 08:35:35', NULL, 13),
                                                                                                                       (10, 'Eos libero debitis excepturi', 'eos-libero-debitis-excepturi', 'Est suscipit quis voluptatibus nemo consequatur voluptatem. Saepe temporibus doloremque temporibus autem et doloribus occaecati unde. Aliquam nostrum sit dolor est omnis aperiam. Ullam earum repellendus unde atque ut omnis in porro. Iste quis culpa aut dolorem ab dolores incidunt.\n\nEius dolores eveniet deserunt reiciendis. In consequatur corrupti ut hic aut. Perferendis veniam eligendi praesentium quaerat optio quaerat in. Nulla maxime accusantium deserunt sunt non distinctio.', 'published', '2026-03-05 08:35:35', NULL, 13),
                                                                                                                       (11, 'Rem officia eum impedit quia', 'rem-officia-eum-impedit-quia', 'Aut sequi maiores possimus illum. Adipisci voluptates qui magnam impedit. Dolor aspernatur id rerum amet alias sunt. Praesentium totam odit modi nemo ipsa.\n\nFacere veritatis quia est quis eligendi sit et. Laboriosam nemo exercitationem quos laudantium omnis. Et nobis dolore quasi qui id. Id sed officiis quo occaecati pariatur dolor nesciunt.', 'published', '2026-03-05 08:35:35', '2025-10-27 22:31:24', 13),
                                                                                                                       (12, 'Optio fuga omnis ipsa', 'optio-fuga-omnis-ipsa', 'Vel est sapiente veniam debitis optio. Ut natus qui voluptatem porro accusamus hic. Neque autem adipisci qui nostrum.\n\nUt amet illo nisi sit sequi. Dicta ut quo eos enim. Eaque qui corrupti sequi beatae. Aut omnis eos et corrupti vel rerum et.', 'published', '2026-03-05 08:35:35', '2025-12-24 18:41:47', 13),
                                                                                                                       (13, 'Blanditiis aut minus', 'blanditiis-aut-minus', 'Id aut et quia autem velit. Rem nostrum veritatis suscipit sit. Recusandae vitae ut sapiente.\n\nEt vero eveniet quia aut qui. Quia nihil magni aliquam. Sunt soluta quia quo tempore facere.', 'published', '2026-03-05 08:35:35', NULL, 14),
                                                                                                                       (14, 'Qui et doloremque repellendus', 'qui-et-doloremque-repellendus', 'Illum molestias minus nesciunt labore ad sapiente. Perspiciatis odit deserunt ullam non quis. Vel dolorem eius aliquam quaerat. Tempore aut consequatur sit esse aliquid est.\n\nQuia et et et consectetur ut quis. Eligendi eius nobis ipsum quae suscipit. Et neque doloribus maxime esse earum enim. Sapiente est aut doloribus magni.', 'published', '2026-03-05 08:35:35', '2025-10-22 12:19:34', 14),
                                                                                                                       (15, 'Eius accusantium et autem qui voluptatem', 'eius-accusantium-et-autem-qui-voluptatem', 'Sed rem unde id nesciunt vel molestiae. Rerum et ut nam quo totam molestias et. Natus dolor dolorum voluptatibus dolores doloribus voluptas et. Quia enim ipsum voluptatum est temporibus eaque alias.\n\nFugiat itaque doloribus culpa iure eveniet quidem id. Ipsum quae et corporis. Dignissimos eligendi voluptatum necessitatibus ea temporibus doloribus et.', 'published', '2026-03-05 08:35:35', '2025-09-27 17:22:23', 14),
                                                                                                                       (16, 'Cupiditate provident voluptatem', 'cupiditate-provident-voluptatem', 'Voluptatem rerum nostrum eaque tenetur doloremque ea in. Molestiae repellendus illum qui. Aliquam itaque quae non velit possimus. Esse quam architecto quod vel sed fugit aut.\n\nConsequatur cumque culpa aspernatur nihil est qui sed. Minima facilis dolore laboriosam quos voluptatum numquam sequi doloribus. Dolorem ut temporibus cupiditate animi nisi. Error distinctio autem quaerat soluta quidem repellendus.', 'published', '2026-03-05 08:35:35', NULL, 14),
                                                                                                                       (17, 'Occaecati consequatur ut corrupti dolores asperiores', 'occaecati-consequatur-ut-corrupti-dolores-asperiores', 'Architecto temporibus exercitationem totam eaque adipisci. Aperiam consequatur sint distinctio ut consequatur officia reprehenderit aut. Omnis sunt iste rem dolorem qui. Veniam nesciunt dolorem quia.\n\nSunt esse sunt asperiores quos quo. Quia fuga perferendis pariatur sed repellendus deleniti. Molestiae maxime veritatis enim iusto quia.', 'published', '2026-03-05 08:35:35', NULL, 15),
                                                                                                                       (18, 'Laborum reprehenderit qui beatae id', 'laborum-reprehenderit-qui-beatae-id', 'Suscipit et autem corporis placeat non. Exercitationem quasi sint dolore vel. Ab voluptas quia nam neque aut et. Saepe tempora dolores suscipit facilis repudiandae.\n\nVoluptas expedita nesciunt fugit voluptas maxime numquam asperiores. Nam autem deleniti aliquid quod ut beatae expedita.', 'published', '2026-03-05 08:35:35', NULL, 15),
                                                                                                                       (19, 'Impedit aut sequi minima', 'impedit-aut-sequi-minima', 'Ut possimus officia reprehenderit quisquam dolore rem molestiae. Maxime sit porro labore alias. Aut possimus ut impedit sapiente neque est. Aliquid necessitatibus quas sapiente ratione illo quis reiciendis.\n\nVeritatis repellendus sequi accusamus corrupti aperiam cum. Consequuntur dolor velit mollitia neque cupiditate. Et ut vitae adipisci et molestiae explicabo. Sunt sit ut iusto sint.', 'published', '2026-03-05 08:35:35', '2025-06-03 05:04:47', 15),
                                                                                                                       (20, 'Quis porro sequi aliquid consequatur', 'quis-porro-sequi-aliquid-consequatur', 'Assumenda dignissimos atque illo numquam. Fugit tenetur dolores soluta sit eos ducimus accusantium. Rerum quia vel sint quisquam consequuntur nam animi. Aut veritatis est in officiis rem.\n\nSit est iure corporis tenetur. Fugit velit aliquam est eos voluptate et odio. Sint adipisci nihil error illo. Blanditiis illo ut cum recusandae.', 'published', '2026-03-05 08:35:35', '2025-05-01 11:48:29', 15),
                                                                                                                       (21, 'Eum aut voluptas et aspernatur quis', 'eum-aut-voluptas-et-aspernatur-quis', 'Magni tempore sed pariatur nisi odit quis. Molestias facere a quia. Esse tenetur aspernatur aliquam ducimus maxime quos.\n\nEx enim quibusdam non sapiente cupiditate aut et. Voluptatem est expedita eaque itaque ad eius. Ipsam nihil reprehenderit perferendis voluptatem quaerat reprehenderit error.', 'published', '2026-03-05 08:35:36', '2025-08-09 18:09:45', 15),
                                                                                                                       (22, 'Odit quia sit', 'odit-quia-sit', 'Expedita quis ratione ex nihil ea nostrum. Possimus omnis et est fugiat repellat. Omnis consequatur consequatur est voluptates.\n\nQuod officia corporis expedita eveniet reiciendis dolores sed. Molestias fugit tempore sit quis dicta nesciunt. Sit sed minima similique praesentium accusamus accusantium. Perspiciatis dignissimos eius quis nihil eos natus blanditiis.', 'published', '2026-03-05 08:35:36', NULL, 16),
                                                                                                                       (23, 'Aut praesentium et', 'aut-praesentium-et', 'Modi animi voluptatum iste sunt aperiam. Optio officia porro nesciunt exercitationem. Voluptas et et dolor eaque tenetur unde. Ea et libero repellat non soluta.\n\nNatus sint veritatis et corrupti. Consequatur qui ea sit quia excepturi aliquam. Soluta quo et est sit est aut voluptatum. Eum quos illum nulla nihil excepturi quidem ut.', 'published', '2026-03-05 08:35:36', NULL, 16),
                                                                                                                       (24, 'Quaerat consequatur rerum consequatur quia totam', 'quaerat-consequatur-rerum-consequatur-quia-totam', 'Possimus quia libero quia distinctio. Dolorum quas qui necessitatibus blanditiis reiciendis. Eum ea expedita aut et qui molestiae. Consequatur velit sed illum soluta at est non.\n\nVeniam in aut quos nam ex voluptatem hic. Suscipit enim ea rerum vel dolor dolorem. Sed sunt sapiente nemo tenetur assumenda omnis corporis dolorem. Eum facilis ipsa laborum quam sunt.', 'published', '2026-03-05 08:35:36', '2025-08-26 00:09:34', 16),
                                                                                                                       (25, 'Facere maiores consectetur quas ut', 'facere-maiores-consectetur-quas-ut', 'Sit provident facere aut quia. Corrupti repellat illo est ipsum. Veniam sint ea eos. Ut tempore delectus hic et culpa.\n\nQuod aperiam eveniet quia eligendi expedita. Dolorem qui qui est voluptatem sunt pariatur eaque. Id sunt inventore soluta non reiciendis.', 'published', '2026-03-05 08:35:36', '2025-08-17 12:52:50', 16),
                                                                                                                       (26, 'Ea explicabo in sequi', 'ea-explicabo-in-sequi', 'Ea ut sit consectetur aut. Vel ratione inventore assumenda. Saepe recusandae repudiandae nemo non ut pariatur. Totam rerum provident fugit ut id tempora. Dolorem dolor vitae tempora.\n\nVoluptatem necessitatibus labore consectetur incidunt incidunt in odio. Et sed voluptatem est cumque consequuntur. Quo porro rerum quaerat similique consequatur.', 'published', '2026-03-05 08:35:36', '2025-12-14 08:05:19', 17),
                                                                                                                       (27, 'Laboriosam voluptate provident alias autem', 'laboriosam-voluptate-provident-alias-autem', 'Officiis commodi soluta qui id in dignissimos. Delectus totam nemo aut nam officia dolores nam. Suscipit repellat eius natus corrupti et dolores. Nobis velit et nostrum pariatur.\n\nQuis tempore sunt est omnis. Qui aspernatur et quibusdam.', 'published', '2026-03-05 08:35:36', '2025-10-20 08:02:00', 17),
                                                                                                                       (28, 'Quod et quisquam similique sit', 'quod-et-quisquam-similique-sit', 'Incidunt nobis delectus nihil dolore at deserunt est cumque. Illo est autem et praesentium est maiores modi. Non nihil nihil deserunt.\n\nAssumenda rerum quia inventore consequuntur. Quo dolorum nesciunt qui ab nesciunt. Minus repudiandae ut repudiandae reprehenderit exercitationem expedita quia. Incidunt natus dicta beatae laborum non repellendus.', 'published', '2026-03-05 08:35:36', NULL, 17),
                                                                                                                       (29, 'Officiis magnam commodi doloremque exercitationem praesentium', 'officiis-magnam-commodi-doloremque-exercitationem-praesentium', 'Et officia quia et voluptatum odio. Voluptatem et molestiae ratione dolores.\n\nId dolor perferendis blanditiis iste labore quibusdam deserunt. Id sint odit eius non id earum placeat. Veritatis reprehenderit explicabo dolorem possimus ea dicta et.', 'published', '2026-03-05 08:35:36', NULL, 17),
                                                                                                                       (30, 'Et maxime est', 'et-maxime-est', 'Dolor consequatur facilis enim quaerat ipsa. Provident ipsa atque saepe temporibus. Consequatur suscipit ducimus consectetur voluptatem est. Eveniet pariatur voluptatem quis nam doloremque voluptas eveniet consectetur.\n\nEaque illum nam tenetur doloribus quas quae magni mollitia. Magnam quia sint qui. Recusandae id sit adipisci dolores consequatur non atque.', 'published', '2026-03-05 08:35:36', '2025-04-16 18:17:20', 17),
                                                                                                                       (31, 'Ea qui molestiae corrupti rerum qui', 'ea-qui-molestiae-corrupti-rerum-qui', 'Temporibus est eligendi tempore voluptatem ut. Nostrum sint atque eaque numquam assumenda voluptatibus. Ut quia nihil dicta sit accusamus incidunt.\n\nEaque numquam repellat voluptas cum cumque saepe. Quo qui iure maiores. Fugit est qui consequuntur corporis id quia. Rerum qui quidem eius quae quasi ut dolores. Ullam ea atque corporis id exercitationem voluptatum voluptates impedit.', 'published', '2026-03-05 08:35:36', '2025-11-05 21:58:44', 18),
                                                                                                                       (32, 'Quo repellat voluptas', 'quo-repellat-voluptas', 'Iusto animi harum incidunt est nihil. Minus aliquam ex velit velit ea.\n\nRepellat amet nihil voluptas et iusto qui quis. Aut eos illo et ipsa molestiae aspernatur et. Nemo delectus voluptate est itaque. Consequatur qui voluptatibus tempore fugit omnis velit consequatur perferendis.', 'published', '2026-03-05 08:35:36', '2025-07-25 21:17:44', 18);

-- --------------------------------------------------------

--
-- Structure de la table `works_user`
--

DROP TABLE IF EXISTS `works_user`;
CREATE TABLE `works_user` (
                              `works_id` int(10) UNSIGNED NOT NULL,
                              `user_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `works_user`
--

INSERT INTO `works_user` (`works_id`, `user_id`) VALUES
                                                     (1, 136),
                                                     (2, 136),
                                                     (3, 136),
                                                     (4, 136),
                                                     (5, 136),
                                                     (5, 137),
                                                     (6, 136),
                                                     (7, 136),
                                                     (7, 137),
                                                     (8, 136),
                                                     (8, 137),
                                                     (9, 136),
                                                     (9, 137),
                                                     (10, 136),
                                                     (11, 136),
                                                     (12, 136),
                                                     (12, 137),
                                                     (12, 138),
                                                     (13, 136),
                                                     (13, 137),
                                                     (14, 136),
                                                     (14, 137),
                                                     (14, 138),
                                                     (15, 136),
                                                     (15, 137),
                                                     (15, 138),
                                                     (16, 136),
                                                     (17, 136),
                                                     (18, 136),
                                                     (19, 136),
                                                     (20, 136),
                                                     (21, 136),
                                                     (21, 137),
                                                     (21, 138),
                                                     (22, 136),
                                                     (23, 136),
                                                     (24, 136),
                                                     (24, 137),
                                                     (25, 136),
                                                     (25, 137),
                                                     (25, 138),
                                                     (26, 136),
                                                     (26, 137),
                                                     (26, 138),
                                                     (27, 136),
                                                     (27, 137),
                                                     (28, 136),
                                                     (29, 136),
                                                     (30, 136),
                                                     (31, 136),
                                                     (31, 137),
                                                     (32, 136),
                                                     (32, 137),
                                                     (32, 138);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `comment`
--
ALTER TABLE `comment`
    ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_9474526CA76ED395` (`user_id`),
  ADD KEY `IDX_9474526CF6CB822A` (`works_id`);

--
-- Index pour la table `comment_rating`
--
ALTER TABLE `comment_rating`
    ADD PRIMARY KEY (`rating_id`,`comment_id`),
  ADD KEY `IDX_129A7E30A32EFC6` (`rating_id`),
  ADD KEY `IDX_129A7E30F8697D13` (`comment_id`);

--
-- Index pour la table `contact_message`
--
ALTER TABLE `contact_message`
    ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_2C9211FEF5675CD0` (`read_by_id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
    ADD PRIMARY KEY (`version`);

--
-- Index pour la table `formation`
--
ALTER TABLE `formation`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_404021BF989D9B62` (`slug`),
  ADD KEY `IDX_404021BFB03A8386` (`created_by_id`),
  ADD KEY `IDX_404021BF896DBBDE` (`updated_by_id`);

--
-- Index pour la table `formation_user`
--
ALTER TABLE `formation_user`
    ADD PRIMARY KEY (`formation_id`,`user_id`),
  ADD KEY `IDX_DA4C33095200282E` (`formation_id`),
  ADD KEY `IDX_DA4C3309A76ED395` (`user_id`);

--
-- Index pour la table `inscription`
--
ALTER TABLE `inscription`
    ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_5E90F6D65200282E` (`formation_id`),
  ADD KEY `IDX_5E90F6D6544F38F5` (`treat_by_id`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
    ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`);

--
-- Index pour la table `page`
--
ALTER TABLE `page`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_140AB620989D9B62` (`slug`);

--
-- Index pour la table `page_user`
--
ALTER TABLE `page_user`
    ADD PRIMARY KEY (`page_id`,`user_id`),
  ADD KEY `IDX_A57CA93C4663E4` (`page_id`),
  ADD KEY `IDX_A57CA93A76ED395` (`user_id`);

--
-- Index pour la table `partenaire`
--
ALTER TABLE `partenaire`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `rating`
--
ALTER TABLE `rating`
    ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_D8892622A76ED395` (`user_id`);

--
-- Index pour la table `rating_works`
--
ALTER TABLE `rating_works`
    ADD PRIMARY KEY (`rating_id`,`works_id`),
  ADD KEY `IDX_6BF30D7FA32EFC6` (`rating_id`),
  ADD KEY `IDX_6BF30D7FF6CB822A` (`works_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`),
  ADD UNIQUE KEY `UNIQ_8D93D64924A232CF` (`user_name`);

--
-- Index pour la table `works`
--
ALTER TABLE `works`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_F6E50243989D9B62` (`slug`),
  ADD KEY `IDX_F6E502435200282E` (`formation_id`);

--
-- Index pour la table `works_user`
--
ALTER TABLE `works_user`
    ADD PRIMARY KEY (`works_id`,`user_id`),
  ADD KEY `IDX_88231830F6CB822A` (`works_id`),
  ADD KEY `IDX_88231830A76ED395` (`user_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `comment`
--
ALTER TABLE `comment`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT pour la table `contact_message`
--
ALTER TABLE `contact_message`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `formation`
--
ALTER TABLE `formation`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `inscription`
--
ALTER TABLE `inscription`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
    MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `page`
--
ALTER TABLE `page`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `partenaire`
--
ALTER TABLE `partenaire`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT pour la table `rating`
--
ALTER TABLE `rating`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;

--
-- AUTO_INCREMENT pour la table `works`
--
ALTER TABLE `works`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `comment`
--
ALTER TABLE `comment`
    ADD CONSTRAINT `FK_9474526CA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_9474526CF6CB822A` FOREIGN KEY (`works_id`) REFERENCES `works` (`id`);

--
-- Contraintes pour la table `comment_rating`
--
ALTER TABLE `comment_rating`
    ADD CONSTRAINT `FK_129A7E30A32EFC6` FOREIGN KEY (`rating_id`) REFERENCES `rating` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_129A7E30F8697D13` FOREIGN KEY (`comment_id`) REFERENCES `comment` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `contact_message`
--
ALTER TABLE `contact_message`
    ADD CONSTRAINT `FK_2C9211FEF5675CD0` FOREIGN KEY (`read_by_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `formation`
--
ALTER TABLE `formation`
    ADD CONSTRAINT `FK_404021BF896DBBDE` FOREIGN KEY (`updated_by_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_404021BFB03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `formation_user`
--
ALTER TABLE `formation_user`
    ADD CONSTRAINT `FK_DA4C33095200282E` FOREIGN KEY (`formation_id`) REFERENCES `formation` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_DA4C3309A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `inscription`
--
ALTER TABLE `inscription`
    ADD CONSTRAINT `FK_5E90F6D65200282E` FOREIGN KEY (`formation_id`) REFERENCES `formation` (`id`),
  ADD CONSTRAINT `FK_5E90F6D6544F38F5` FOREIGN KEY (`treat_by_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `page_user`
--
ALTER TABLE `page_user`
    ADD CONSTRAINT `FK_A57CA93A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_A57CA93C4663E4` FOREIGN KEY (`page_id`) REFERENCES `page` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `rating`
--
ALTER TABLE `rating`
    ADD CONSTRAINT `FK_D8892622A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `rating_works`
--
ALTER TABLE `rating_works`
    ADD CONSTRAINT `FK_6BF30D7FA32EFC6` FOREIGN KEY (`rating_id`) REFERENCES `rating` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_6BF30D7FF6CB822A` FOREIGN KEY (`works_id`) REFERENCES `works` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `works`
--
ALTER TABLE `works`
    ADD CONSTRAINT `FK_F6E502435200282E` FOREIGN KEY (`formation_id`) REFERENCES `formation` (`id`);

--
-- Contraintes pour la table `works_user`
--
ALTER TABLE `works_user`
    ADD CONSTRAINT `FK_88231830A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_88231830F6CB822A` FOREIGN KEY (`works_id`) REFERENCES `works` (`id`) ON DELETE CASCADE;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;
