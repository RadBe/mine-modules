<?php


namespace App\Shop\Controllers\Admin;


use App\Core\Http\AdminController;
use App\Core\Http\ModuleInstallation;
use App\Core\Http\Request;
use App\Shop\Config;
use App\Shop\Enchanting\Enchant;
use App\Shop\Models\CategoryModel;
use App\Shop\Models\ProductModel;

class InstallController extends AdminController implements ModuleInstallation
{
    /**
     * @inheritDoc
     */
    public function index(): void
    {
        /* @var CategoryModel $categoryModel */
        $categoryModel = $this->app->make(CategoryModel::class);
        $sql = "INSERT INTO `{$categoryModel->getTable()}` (`id`, `server_id`, `name`, `enabled`) VALUES
(1, NULL, 'Блоки', 1),
(2, NULL, 'Инструменты', 1)";
        $categoryModel->getConnection()->execute($sql);

        /* @var ProductModel $productModel */
        $productModel = $this->app->make(ProductModel::class);
        $sql = "INSERT INTO `{$productModel->getTable()}` (`id`, `category_id`, `server_id`, `name`, `block_id`, `amount`, `enchants`, `price`, `enabled`, `img`, `buys`, `created_at`) VALUES
(1, 1, NULL, 'Камень', 'STONE', 64, NULL, 2, 1, '1_sCu.png', 1, '2021-01-21 13:30:10'),
(2, 1, NULL, 'Блок травы', 'GRASS', 64, NULL, 2, 1, '2_6JY.png', 0, '2021-01-21 13:32:27'),
(3, 1, NULL, 'Земля', 'DIRT', 64, NULL, 1, 1, '3_zUc.png', 0, '2021-01-21 13:32:51'),
(4, 1, NULL, 'Булыжник', 'COBBLESTONE', 64, NULL, 1, 1, '4_js6.png', 0, '2021-01-21 13:35:16'),
(5, 1, NULL, 'Дубовые доски', 'PLANKS', 64, NULL, 3, 1, '5_wm4.png', 0, '2021-01-21 13:38:47'),
(6, 1, NULL, 'Еловые доски', 'PLANKS:1', 64, NULL, 3, 1, '6_9Rl.png', 0, '2021-01-21 13:39:48'),
(7, 1, NULL, 'Березовые доски', 'PLANKS:2', 64, NULL, 3, 1, '7_Jry.png', 0, '2021-01-21 13:40:24'),
(8, 1, NULL, 'Тропические доски', 'PLANKS:3', 64, NULL, 3, 1, '8_nAq.png', 0, '2021-01-21 13:41:00'),
(9, 1, NULL, 'Саженец дуба', 'SAPLING', 12, NULL, 1, 1, '9_E8Q.png', 0, '2021-01-21 13:45:45'),
(10, 1, NULL, 'Саженец ели', 'SAPLING:1', 12, NULL, 1, 1, '10_OSq.png', 0, '2021-01-21 13:46:19'),
(11, 1, NULL, 'Саженец березы', 'SAPLING:2', 12, NULL, 1, 1, '11_gT6.png', 0, '2021-01-21 13:46:46'),
(12, 1, NULL, 'Саженец тропического дерева', 'SAPLING:3', 12, NULL, 1, 1, '12_zts.png', 0, '2021-01-21 13:47:29'),
(13, 1, NULL, 'Бедрок', 'BEDROCK', 1, NULL, 999, 1, '13_iVK.png', 0, '2021-01-21 13:48:34'),
(14, 1, NULL, 'Песок', 'SAND', 64, NULL, 1, 1, '14_HIY.png', 0, '2021-01-21 13:49:07'),
(15, 1, NULL, 'Гравий', 'GRAVEL', 64, NULL, 2, 1, '15_RYG.png', 0, '2021-01-21 13:49:53'),
(16, 1, NULL, 'Золотая руда', 'GOLD_ORE', 8, NULL, 3, 1, '16_hTH.png', 0, '2021-01-21 13:50:50'),
(17, 1, NULL, 'Железная руда', 'IRON_ORE', 8, NULL, 2, 1, '17_yXl.png', 0, '2021-01-21 13:51:43'),
(18, 1, NULL, 'Угольная руда', 'COAL_ORE', 16, NULL, 4, 1, '18_4ya.png', 0, '2021-01-21 13:52:33'),
(19, 1, NULL, 'Дуб', 'LOG', 64, NULL, 4, 1, '19_l4U.png', 0, '2021-01-21 13:53:18'),
(20, 1, NULL, 'Ель', 'LOG:1', 64, NULL, 4, 1, '20_udp.png', 0, '2021-01-21 13:53:49'),
(21, 1, NULL, 'Береза', 'LOG:2', 64, NULL, 4, 1, '21_2IU.png', 0, '2021-01-21 13:54:10'),
(22, 1, NULL, 'Тропическое дерево', 'LOG:3', 64, NULL, 4, 1, '22_aGL.png', 0, '2021-01-21 13:54:48'),
(23, 1, NULL, 'Дубовая листва', 'LEAVES', 64, NULL, 3, 1, '23_uZu.png', 0, '2021-01-21 13:55:41'),
(24, 1, NULL, 'Хвоя', 'LEAVES:1', 64, NULL, 3, 1, '24_OHD.png', 0, '2021-01-21 13:56:08'),
(25, 1, NULL, 'Березовая листва', 'LEAVES:2', 64, NULL, 3, 1, '25_4yq.png', 0, '2021-01-21 13:56:36'),
(26, 1, NULL, 'Тропическая листва', 'LEAVES:3', 64, NULL, 3, 1, '26_pvr.png', 0, '2021-01-21 13:56:57'),
(27, 1, NULL, 'Губка', 'SPONGE', 1, NULL, 16, 1, '27_ECi.png', 0, '2021-01-21 13:57:39'),
(28, 1, NULL, 'Стекло', 'GLASS', 32, NULL, 4, 1, '28_EV1.png', 0, '2021-01-21 13:58:14'),
(29, 1, NULL, 'Лазуритовая руда', 'LAPIS_ORE', 10, NULL, 2, 1, '29_OqD.png', 0, '2021-01-21 13:59:15'),
(30, 1, NULL, 'Лазуритовый блок', 'LAPIS_BLOCK', 1, NULL, 2, 1, '30_C2E.png', 0, '2021-01-21 13:59:56'),
(31, 1, NULL, 'Раздатчик', 'DISPENSER', 1, NULL, 1, 1, '31_RBQ.png', 0, '2021-01-21 14:00:54'),
(32, 1, NULL, 'Песчанник', 'SANDSTONE', 16, NULL, 2, 1, '32_gSV.png', 0, '2021-01-21 14:01:25'),
(33, 1, NULL, 'Резной песчаник', 'SANDSTONE:1', 16, NULL, 2, 1, '33_5Iv.png', 0, '2021-01-21 14:01:55'),
(34, 1, NULL, 'Гладкий песчаник', 'SANDSTONE:2', 16, NULL, 2, 1, '34_EVH.png', 0, '2021-01-21 14:02:24'),
(35, 1, NULL, 'Нотный блок', 'NOTEBLOCK', 2, NULL, 1, 1, '35_nG7.png', 0, '2021-01-21 14:03:32'),
(36, 1, NULL, 'Липкий поршень', 'STICKY_PISTON', 4, NULL, 2, 1, '36_kIH.png', 0, '2021-01-21 14:04:58'),
(37, 1, NULL, 'Паутина', 'WEB', 4, NULL, 10, 1, '37_QkV.png', 0, '2021-01-21 14:05:27'),
(38, 1, NULL, 'Высокая трава', 'TALLGRASS:1', 4, NULL, 2, 1, '38_zaM.png', 0, '2021-01-21 14:06:24'),
(39, 1, NULL, 'Поршень', 'PISTON', 4, NULL, 2, 1, '39_v0A.png', 0, '2021-01-21 14:07:05'),
(40, 1, NULL, 'Шерсть', 'WOOL', 16, NULL, 3, 1, '40_ChE.png', 0, '2021-01-21 14:07:35'),
(41, 1, NULL, 'Оранжевая шерсть', 'WOOL:1', 16, NULL, 3, 1, '41_R17.png', 0, '2021-01-21 14:08:35'),
(42, 1, NULL, 'Пурпурная шерсть', 'WOOL:2', 16, NULL, 3, 1, '42_Z5o.png', 0, '2021-01-21 14:09:13'),
(43, 1, NULL, 'Голубая шерсть', 'WOOL:3', 16, NULL, 3, 1, '43_8pT.png', 0, '2021-01-21 14:10:10'),
(44, 1, NULL, 'Желтая шерсть', 'WOOL:4', 16, NULL, 3, 1, '44_N1d.png', 0, '2021-01-21 14:10:37'),
(45, 1, NULL, 'Лаймовая шерсть', 'WOOL:5', 16, NULL, 3, 1, '45_GGn.png', 0, '2021-01-21 14:11:00'),
(46, 1, NULL, 'Розовая шерсть', 'WOOL:6', 16, NULL, 3, 1, '46_xtp.png', 0, '2021-01-21 14:11:24'),
(47, 1, NULL, 'Серая шерсть', 'WOOL:7', 16, NULL, 3, 1, '47_Zq0.png', 0, '2021-01-21 14:11:52'),
(48, 1, NULL, 'Светло-серая шерсть', 'WOOL:8', 16, NULL, 3, 1, '48_o72.png', 0, '2021-01-21 14:12:19'),
(49, 1, NULL, 'Бирюзовая шерсть', 'WOOL:9', 16, NULL, 3, 1, '49_GD1.png', 0, '2021-01-21 14:12:50'),
(50, 1, NULL, 'Фиолетовая шерсть', 'WOOL:10', 16, NULL, 3, 1, '50_wrW.png', 0, '2021-01-21 14:13:36'),
(51, 1, NULL, 'Синяя шерсть', 'WOOL:11', 16, NULL, 3, 1, '51_bkH.png', 0, '2021-01-21 14:13:59'),
(52, 1, NULL, 'Коричневая шерсть', 'WOOL:12', 16, NULL, 3, 1, '52_vKK.png', 0, '2021-01-21 14:14:40'),
(53, 1, NULL, 'Зеленая шерсть', 'WOOL:13', 16, NULL, 3, 1, '53_vVf.png', 0, '2021-01-21 14:15:14'),
(54, 1, NULL, 'Красная шерсть', 'WOOL:14', 16, NULL, 3, 1, '54_FXb.png', 0, '2021-01-21 14:15:33'),
(55, 1, NULL, 'Черная шерсть', 'WOOL:15', 16, NULL, 3, 1, '55_stg.png', 0, '2021-01-21 14:15:57'),
(56, 1, NULL, 'Одуванчик', 'YELLOW_FLOWER', 8, NULL, 1, 1, '56_aRX.png', 0, '2021-01-21 14:16:56'),
(57, 1, NULL, 'Мак', 'RED_FLOWER', 8, NULL, 1, 1, '57_sc4.png', 0, '2021-01-21 14:17:19'),
(58, 1, NULL, 'Гриб', 'BROWN_MUSHROOM', 8, NULL, 2, 1, '58_byE.png', 0, '2021-01-21 14:18:42'),
(59, 1, NULL, 'Мухомор', 'RED_MUSHROOM', 8, NULL, 2, 1, '59_PYd.png', 0, '2021-01-21 14:19:14'),
(60, 1, NULL, 'Золотой блок', 'GOLD_BLOCK', 2, NULL, 8, 1, '60_UdN.png', 0, '2021-01-21 14:19:51'),
(61, 1, NULL, 'Железный блок', 'IRON_BLOCK', 2, NULL, 8, 1, '61_R2P.png', 0, '2021-01-21 14:20:13'),
(62, 1, NULL, 'Кирпичи', 'BRICK_BLOCK', 16, NULL, 2, 1, '62_MSz.png', 0, '2021-01-21 14:21:12'),
(63, 1, NULL, 'Динамит', 'TNT', 4, NULL, 2, 1, '63_B99.png', 0, '2021-01-21 14:21:45'),
(64, 1, NULL, 'Книжная полка', 'BOOKSHELF', 4, NULL, 6, 1, '64_Jzl.png', 0, '2021-01-21 14:22:21'),
(65, 1, NULL, 'Замшелый булыжник', 'MOSSY_COBBLESTONE', 4, NULL, 2, 1, '65_Lxp.png', 0, '2021-01-21 14:22:52'),
(66, 1, NULL, 'Обсидиан', 'OBSIDIAN', 4, NULL, 8, 1, '66_EBz.png', 0, '2021-01-21 14:23:25'),
(67, 1, NULL, 'Факел', 'TORCH', 32, NULL, 5, 1, '67_XH2.png', 0, '2021-01-21 14:23:56'),
(68, 1, NULL, 'Алмазная руда', 'DIAMOND_ORE', 2, NULL, 10, 1, '68_9UL.png', 0, '2021-01-21 14:24:35'),
(69, 1, NULL, 'Алмазный блок', 'DIAMOND_BLOCK', 2, NULL, 20, 1, '69_bW7.png', 0, '2021-01-21 14:25:05'),
(70, 1, NULL, 'Красная руда', 'REDSTONE_ORE', 6, NULL, 8, 1, '70_fw1.png', 0, '2021-01-21 14:25:56'),
(71, 1, NULL, 'Красный факел', 'REDSTONE_TORCH', 16, NULL, 6, 1, '71_Tu8.png', 0, '2021-01-21 14:26:38'),
(72, 1, NULL, 'Лёд', 'ICE', 32, NULL, 2, 1, '72_jss.png', 0, '2021-01-21 14:27:26'),
(73, 1, NULL, 'Снег', 'SNOW', 32, NULL, 2, 1, '73_IFn.png', 0, '2021-01-21 14:28:02'),
(74, 1, NULL, 'Кактус', 'CACTUS', 8, NULL, 2, 1, '74_j0G.png', 0, '2021-01-21 14:28:36'),
(75, 1, NULL, 'Глина', 'CLAY', 16, NULL, 4, 1, '75_NHK.png', 0, '2021-01-21 14:29:08'),
(76, 1, NULL, 'Проигрыватель', 'JUKEBOX', 1, NULL, 8, 1, '76_Y5o.png', 0, '2021-01-21 14:29:47'),
(77, 1, NULL, 'Забор', 'FENCE', 8, NULL, 1, 1, '77_v2o.png', 0, '2021-01-21 14:30:27'),
(78, 1, NULL, 'Тыква', 'PUMPKIN', 4, NULL, 2, 1, '78_Q8K.png', 0, '2021-01-21 14:30:55'),
(79, 1, NULL, 'Адский камень', 'NETHERRACK', 16, NULL, 4, 1, '79_cvz.png', 0, '2021-01-21 14:31:34'),
(80, 1, NULL, 'Песок душ', 'SOUL_SAND', 8, NULL, 2, 1, '80_Dze.png', 0, '2021-01-21 14:32:04'),
(81, 1, NULL, 'Светящийся камень', 'GLOWSTONE', 8, NULL, 6, 1, '81_byF.png', 0, '2021-01-21 14:32:36'),
(82, 1, NULL, 'Светильник Джека', 'LIT_PUMPKIN', 4, NULL, 1, 1, '82_m6d.png', 0, '2021-01-21 14:33:10'),
(83, 1, NULL, 'Люк', 'TRAPDOOR', 16, NULL, 4, 1, '83_DCI.png', 0, '2021-01-21 14:33:56'),
(84, 1, NULL, 'Каменные кирпичи', 'STONEBRICK', 32, NULL, 10, 1, '84_pm8.png', 0, '2021-01-21 14:34:47'),
(85, 1, NULL, 'Замшелые каменные кирпичи', 'STONEBRICK:1', 32, NULL, 12, 1, '85_ybC.png', 0, '2021-01-21 14:35:17'),
(86, 1, NULL, 'Потресканные каменные кирпичи', 'STONEBRICK:2', 32, NULL, 12, 1, '86_1eV.png', 0, '2021-01-21 14:35:42'),
(87, 1, NULL, 'Резные каменные кирпичи', 'STONEBRICK:3', 32, NULL, 12, 1, '87_nJO.png', 0, '2021-01-21 14:36:02'),
(88, 1, NULL, 'Железные прутья', 'IRON_BARS', 8, NULL, 4, 1, '88_PAS.png', 0, '2021-01-21 14:36:31'),
(89, 1, NULL, 'Стеклянная панель', 'GLASS_PANE', 32, NULL, 8, 1, '89_6jN.png', 0, '2021-01-21 14:37:00'),
(90, 1, NULL, 'Арбуз', 'MELON_BLOCK', 4, NULL, 2, 1, '90_vRL.png', 0, '2021-01-21 14:37:39'),
(91, 1, NULL, 'Лоза', 'VINE', 16, NULL, 6, 1, '91_g1u.png', 0, '2021-01-21 14:38:07'),
(92, 1, NULL, 'Мицелий', 'MYCELIUM', 4, NULL, 8, 1, '92_xN3.png', 0, '2021-01-21 14:38:50'),
(93, 1, NULL, 'Кувшинка', 'WATERLILY', 24, NULL, 6, 1, '93_3yt.png', 0, '2021-01-21 14:39:21'),
(94, 1, NULL, 'Адские кирпичи', 'NETHER_BRICK', 16, NULL, 4, 1, '94_pxv.png', 0, '2021-01-21 14:40:01'),
(95, 1, NULL, 'Адский забор', 'NETHER_BRICK_FENCE', 8, NULL, 2, 1, '95_sAF.png', 0, '2021-01-21 14:40:37'),
(96, 1, NULL, 'Стол зачарований', 'ENCHANTING_TABLE', 1, NULL, 20, 1, '96_U7J.png', 0, '2021-01-21 14:41:15'),
(97, 1, NULL, 'Эндерняк', 'END_STONE', 16, NULL, 10, 1, '97_MRg.png', 0, '2021-01-21 14:41:48'),
(98, 1, NULL, 'Лампа', 'REDSTONE_LAMP', 4, NULL, 2, 1, '98_LP4.png', 0, '2021-01-21 14:42:30'),
(99, 1, NULL, 'Изумрудная руда', 'EMERALD_ORE', 2, NULL, 6, 1, '99_aY1.png', 0, '2021-01-21 14:43:07'),
(100, 1, NULL, 'Сундук Эндера', 'ENDER_CHEST', 1, NULL, 20, 1, '100_MrP.png', 0, '2021-01-21 14:43:40'),
(101, 1, NULL, 'Крюк', 'TRIPWIRE_HOOK', 16, NULL, 4, 1, '101_k6K.png', 0, '2021-01-21 14:44:15'),
(102, 1, NULL, 'Изумрудный блок', 'EMERALD_BLOCK', 2, NULL, 18, 1, '102_mBS.png', 0, '2021-01-21 14:44:45'),
(103, 1, NULL, 'Маяк', 'BEACON', 1, NULL, 20, 1, '103_knn.png', 0, '2021-01-21 14:45:13'),
(104, 1, NULL, 'Наковальня', 'ANVIL', 1, NULL, 10, 1, '104_KqT.png', 0, '2021-01-21 14:45:52'),
(105, 1, NULL, 'Датчик дневного света', 'DAYLIGHT_DETECTOR', 2, NULL, 4, 1, '105_YFk.png', 0, '2021-01-21 14:46:26'),
(106, 1, NULL, 'Блок красного камня', 'REDSTONE_BLOCK', 2, NULL, 8, 1, '106_xHS.png', 0, '2021-01-21 14:47:04'),
(107, 1, NULL, 'Кварцевая руда', 'QUARTZ_ORE', 6, NULL, 3, 1, '107_4uk.png', 0, '2021-01-21 14:47:57'),
(108, 1, NULL, 'Воронка', 'HOPPER', 2, NULL, 6, 1, '108_f1t.png', 0, '2021-01-21 14:48:26'),
(109, 1, NULL, 'Кварцевый блок', 'QUARTZ_BLOCK', 4, NULL, 2, 1, '109_d36.png', 0, '2021-01-21 14:49:08'),
(110, 1, NULL, 'Выбрасыватель', 'DROPPER', 1, NULL, 1, 1, '110_ogu.png', 0, '2021-01-21 14:50:55'),
(111, 1, NULL, 'Белая обожжённая глина', 'STAINED_HARDENED_CLAY', 16, NULL, 4, 1, '111_wS6.png', 0, '2021-01-21 14:52:03'),
(112, 1, NULL, 'Оранжевая обожжённая глина', 'STAINED_HARDENED_CLAY:1', 16, NULL, 4, 1, '112_X4o.png', 0, '2021-01-21 14:52:28'),
(113, 1, NULL, 'Пурпурная обожжённая глина', 'STAINED_HARDENED_CLAY:2', 16, NULL, 4, 1, '113_ZHH.png', 0, '2021-01-21 14:52:52'),
(114, 1, NULL, 'Голубая обожжённая глина', 'STAINED_HARDENED_CLAY:3', 16, NULL, 4, 1, '114_XFF.png', 0, '2021-01-21 14:53:14'),
(115, 1, NULL, 'Жёлтая обожжённая глина', 'STAINED_HARDENED_CLAY:4', 16, NULL, 4, 1, '115_imN.png', 0, '2021-01-21 14:53:51'),
(116, 1, NULL, 'Зелёная обожжённая глина', 'STAINED_HARDENED_CLAY:5', 16, NULL, 4, 1, '116_OWX.png', 0, '2021-01-21 14:54:14'),
(117, 1, NULL, 'Розовая обожжённая глина', 'STAINED_HARDENED_CLAY:6', 16, NULL, 4, 1, '117_EYt.png', 0, '2021-01-21 14:55:08'),
(118, 1, NULL, 'Серая обожжённая глина', 'STAINED_HARDENED_CLAY:7', 16, NULL, 4, 1, '118_0ev.png', 0, '2021-01-21 14:55:40'),
(119, 1, NULL, 'Светло-серая обожжённая глина', 'STAINED_HARDENED_CLAY:8', 16, NULL, 4, 1, '119_3TD.png', 0, '2021-01-21 14:56:10'),
(120, 1, NULL, 'Бирюзовая обожжённая глина', 'STAINED_HARDENED_CLAY:9', 16, NULL, 4, 1, '120_I32.png', 0, '2021-01-21 14:56:47'),
(121, 1, NULL, 'Пурпурная обожжённая глина', 'STAINED_HARDENED_CLAY:10', 16, NULL, 4, 1, '121_gtv.png', 0, '2021-01-21 14:57:28'),
(122, 1, NULL, 'Синяя обожжённая глина', 'STAINED_HARDENED_CLAY:11', 16, NULL, 4, 1, '122_j29.png', 0, '2021-01-21 14:57:57'),
(123, 1, NULL, 'Коричневая обожжённая глина', 'STAINED_HARDENED_CLAY:12', 16, NULL, 4, 1, '123_YOp.png', 1, '2021-01-21 14:58:20'),
(124, 1, NULL, 'Зелёная обожжённая глина', 'STAINED_HARDENED_CLAY:13', 16, NULL, 4, 1, '124_pMi.png', 0, '2021-01-21 14:58:44'),
(125, 1, NULL, 'Красная обожжённая глина', 'STAINED_HARDENED_CLAY:14', 16, NULL, 4, 1, '125_t5H.png', 0, '2021-01-21 14:59:10'),
(126, 1, NULL, 'Чёрная обожжённая глина', 'STAINED_HARDENED_CLAY:15', 16, NULL, 4, 1, '126_RCX.png', 0, '2021-01-21 14:59:31'),
(127, 1, NULL, 'Сноп сена', 'HAY_BLOCK', 8, NULL, 2, 1, '127_5JU.png', 1, '2021-01-21 15:02:13'),
(128, 2, NULL, 'Железная лопата', 'IRON_SHOVEL', 1, NULL, 4, 1, '128_ToV.png', 0, '2021-01-21 15:04:43'),
(129, 2, NULL, 'Железная кирка', 'IRON_PICKAXE', 1, NULL, 6, 1, '129_atz.png', 0, '2021-01-21 15:05:21'),
(130, 2, NULL, 'Железный топор', 'IRON_AXE', 1, NULL, 5, 1, '130_XOP.png', 1, '2021-01-21 15:05:50');";
        $productModel->getConnection()->execute($sql);

        /* @var Config $config */
        $config = $this->module->getConfig();
        $config->addEnchant(new Enchant(34, 'Прочность'), null);
        $config->addEnchant(new Enchant(0, 'Защита'), null);
        $config->addEnchant(new Enchant(1, 'Огнеупорность'), null);
        $config->addEnchant(new Enchant(2, 'Невесомость'), null);
        $config->addEnchant(new Enchant(3, 'Взрывоустойчивость'), null);
        $config->addEnchant(new Enchant(4, 'Снарядостойкость'), null);
        $config->addEnchant(new Enchant(5, 'Подводное дыхание'), null);
        $config->addEnchant(new Enchant(6, 'Подводник'), null);
        $config->addEnchant(new Enchant(7, 'Шипы'), null);
        $config->addEnchant(new Enchant(16, 'Острота'), null);
        $config->addEnchant(new Enchant(17, 'Небесная кара'), null);
        $config->addEnchant(new Enchant(18, 'Гибель насекомых'), null);
        $config->addEnchant(new Enchant(19, 'Отдача'), null);
        $config->addEnchant(new Enchant(20, 'Заговор огня'), null);
        $config->addEnchant(new Enchant(21, 'Добыча'), null);
        $config->addEnchant(new Enchant(48, 'Сила'), null);
        $config->addEnchant(new Enchant(49, 'Откидывание'), null);
        $config->addEnchant(new Enchant(50, 'Горящая стрела'), null);
        $config->addEnchant(new Enchant(51, 'Бесконечность'), null);
        $config->addEnchant(new Enchant(32, 'Эффективность'), null);
        $config->addEnchant(new Enchant(33, 'Шёлковое касание'), null);
        $config->addEnchant(new Enchant(35, 'Удача'), null);
        $config->addEnchant(new Enchant(61, 'Морская удача'), null);
        $config->addEnchant(new Enchant(62, 'Приманка'), null);
        $config->addEnchant(new Enchant(10, 'Проклятие несъёмности'), null);
        $config->addEnchant(new Enchant(71, 'Проклятье утраты'), null);
        $config->addEnchant(new Enchant(8, 'Подводная ходьба'), null);
        $config->addEnchant(new Enchant(9, 'Ледоход'), null);
        $config->addEnchant(new Enchant(70, 'Починка'), null);
        $config->addEnchant(new Enchant(22, 'Разящий клинок'), null);

        $this->module->setInstalled(true);
        $this->module->setEnabled(true);
        $this->updateModule();

        $this->redirect(admin_url('shop'));
    }

    /**
     * @inheritDoc
     */
    public function install(Request $request): void
    {
        //do nothing
    }
}
