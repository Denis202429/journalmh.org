using System;
using System.Collections.Generic;
using System.Diagnostics;
using System.IO;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace MorfParsingLibrary
{
    class AffixInfoProvider
    {
        //ФУНКЦИИ ПРОВЕРКИ АФФИКСОВ
        //перечисления для указания типа поиска и типа восстановления
        public enum SearchMode
        {
            OnlyGlagol, NotGlagol, Any, Prichastie, DeePrichastie, Prilagatelnoe, NotNarechie, NotPril,
            NotNarechieGlagol, SuschGlagol, Chislitelnoe, deenoun, AbsolutelyAny
        }
        public enum RecoveryMode
        {
            With, Without, Both
        }
        //Метод присоединения нового аффикса к юниту аффиксов
        public static string AddAffixToUnit(string aff, string AffUnit)
        {
            if (!(AffUnit.Length == 0))
            {
                if (AffUnit[AffUnit.Length - 1] == '|')
                {
                    AffUnit = AffUnit.Substring(0, AffUnit.Length - 1);
                }
            }

            if (aff.StartsWith("|"))
            {
                return aff + AffUnit;
            }
            return ("|" + aff + AffUnit);
        }
        //Метод проверки "подходимости" аффикса к аффиксу
        public static bool AffixesSuitable(string aff, string AffUnit)
        {
            //типы
            string afftype, unittype;
            bool decision = false;
            //Уровень аффикса
            int afflevel = LevelOfSingleAffix(aff, out afftype);
            //Уровень юнита
            int unitlevel = LevelOfAffUnit(AffUnit, out unittype);
            

            //Trace.WriteLine(aff + ":" + "уровень:" + afflevel + "тип:" + afftype);
            //Trace.WriteLine(AffUnit + ":" + "уровень:" + unitlevel + "тип:" + unittype);
            if (afflevel < unitlevel)
            {
                if ((afftype == unittype) || (afftype == Constants.ANY) || (unittype == Constants.ANY))
                {
                    decision = true;
                }
            }
            //Trace.WriteLine(decision);

            return decision;

        }
        //Метод определения "уровня", типа единичного аффикса
        public static int LevelOfSingleAffix(string AffixForCheck, out string singleafftype)
        {
            singleafftype = Constants.ANY;
            int level = 888;
            
            string[] affmas = AffixForCheck.Split(new char[] { '|' }, StringSplitOptions.RemoveEmptyEntries);
            switch (AffixForCheck)
            {
                case "": level = 100;
                    break;
                case "|": level = 90;
                    break;
                default:

                    for (int i = MorfParser.Affixes.Length - 1; i >= 0; i--)
                    {
                        string[] affarr = MorfParser.Affixes[i].Split(new char[] { ',', ' ' }, StringSplitOptions.RemoveEmptyEntries);
                        for (int j = 0; j < affarr.Length; j++)
                        {
                            if (affarr[j] == affmas[0])
                            {
                                singleafftype = affarr[affarr.Length - 1];
                                return i;                               
                            }

                        }
                    }
                    
                    break;
            }
            return level;
        }
        //Метод определения "уровня", типа юнита аффиксов
        public static int LevelOfAffUnit(string AffUnitForCheck, out string affunittype)
        {
            affunittype = Constants.ANY;

            if (AffUnitForCheck == "") return 100;
            if (AffUnitForCheck == "|") return 90;

            string[] demo = { Constants.ONLYGLAGOL, Constants.NOTGLAGOL };
            
            string singleafftype;
            string[] affmas = AffUnitForCheck.Split(new char[] { '|' }, StringSplitOptions.RemoveEmptyEntries);

            int level = LevelOfSingleAffix(affmas[0], out singleafftype);
            
            for (int i = 0; i < affmas.Length; i++)
            {
                LevelOfSingleAffix(affmas[i], out singleafftype);
                if (demo.Contains(singleafftype))
                {
                    affunittype = singleafftype;
                    return level;
                }
            }
            return level;

            
        }
        //Метод определения типа поиска в словаре
        public  static SearchMode TypeOfSearch(string AffUnit)
        {
            SearchMode mode = SearchMode.Any;
            string afftype;
            int level = LevelOfSingleAffix(AffUnit, out afftype);

            if (level >= 50) return mode;


            string[] aff = AffUnit.Split(new char[] { '|' }, StringSplitOptions.RemoveEmptyEntries);
            for (int i = 0; i < aff.Length; i++)
            {
                LevelOfSingleAffix(aff[i], out afftype);
                switch (aff[i])
                {
                    case "ни":
                    case "акан":
                    case "екен":
                    case "аканни":
                    case "екенни":
                    case "малли":
                    case "мелли":
                    case "малла":
                    case "мелле":
                    case "манни":
                    case "менни":
                    case "нӑ":
                    case "нӗ":
                    case "ас":
                    case "ес":
                    case "асси":
                    case "есси":
                    case "асшӑн":
                    case "есшӗн":
                    //case "ен":
                    //case "ан":
                    //case "мен":
                    //case "ман":
                    case "нӑҫем":
                    case "нӗҫем":
                        mode = SearchMode.Prichastie;
                        goto metka;
                    case "са":
                    case "се":
                    case "сан":
                    case "сассӑн":
                    case "сессӗн":
                    case "масӑр":
                    case "месӗр":
                        mode = SearchMode.DeePrichastie;
                        goto metka;
                    case "а":
                    case "е":
                    case "сен":
                        mode = SearchMode.deenoun;
                        goto metka;
                    case "лӑ":
                    case "лӗ":
                    case "ллӑ":
                    case "ллӗ":
                    case "ли":
                    case "лли":
                        mode = SearchMode.Prilagatelnoe;
                        goto metka;
                    case "мӑш":
                    case "мӗш":
                        mode = SearchMode.Chislitelnoe;
                        goto metka;
                    case "на":
                    case "не":
                        mode = SearchMode.NotNarechieGlagol;
                        goto metka;
                    //case "ах":
                    //case "ех":
                    //case "х":
                    //    mode = SearchMode.AbsolutelyAny;
                    //    goto metka;
                    default:
                        break;
                }

                switch (afftype)
                {
                    case Constants.ONLYGLAGOL:
                    case Constants.NOTGLAGOL:
                        mode = (SearchMode)Enum.Parse(typeof(SearchMode), afftype);
                        break;
                    default:
                        break;
                }
            }
        metka:
            return mode;
        }
        //Метод определения типа восстановления корня
        public static RecoveryMode TypeOfRecovery(string AffUnit)
        {
            RecoveryMode mode = RecoveryMode.Both;
            //выделяем первый аффикс
            string aff = AffUnit.Split(new char[] { '|' }, StringSplitOptions.RemoveEmptyEntries)[0];

            switch (aff)
            {
                case "и":
                case "у":
                    mode = RecoveryMode.With;
                    break;
                case "м":
                case "а":
                case "е":
                case "ти":
                case "ри":
                case "рен":
                case "аттӑм":
                case "еттӗм":
                case "аттӑн":
                case "еттӗн":
                case "ттӑм":
                case "ттӗм":
                case "ттӑн":
                case "ттӗн":
                case "атчӗҫ":
                case "етчӗҫ":
                case "тчӗҫ":
                case "сассӑн":
                case "тӑп":
                case "тӗп":
                case "тӑн":
                case "тӗн":
                case "атӑп":
                case "етӗп":
                case "атӑн":
                case "етӗн":
                case "тпӑр":
                case "тпӗр":
                case "атпӑр":
                case "етпӗр":
                //case "аҫ":
                //case "еҫ":
                case "атт":
                case "етт":
                case "тан":
                case "тен":
                case "та":
                case "те":
                case "ла":
                case "ле":
                case "сӑр":
                case "сӗр":
                case "сӑмӑр":
                case "сӗмӗр":
                case "ам":
                case "ем":
                case "ӑм":
                case "ӗм":
                case "тӑм":
                case "тӗм":
                case "на":
                case "не":
                case "ра":
                case "ре":
                case "сен":
                case "хи":
                case "ччен":
                case "чен":
                case "серен":
                case "сем":
                case "н":
                case "алла":
                case "елле":
                case "па":
                case "пе":
                case "сам":
                case "ран":
                case "сан":
                case "сене":
                case "ма":
                case "ме":
                case "масӑр":
                case "месӗр":
                //case "ат":мешает при корне "выля"
                case "ет":
                    mode = RecoveryMode.Without;
                    break;
            }
            return mode;
        }
        //определение искомой части речи
        public static string PatternPicker(string AffUnit)
        {
            SearchMode sm = TypeOfSearch(AffUnit);

            Trace.WriteLine("Тип поиска: [" + sm + "]");

            string pattern = "";
            switch (sm)
            {
                case SearchMode.OnlyGlagol:
                    pattern =  Constants.VERB + "|Same";
                    break;
                case SearchMode.NotGlagol:
                    pattern = Constants.NOUN +  "," + Constants.NUMERIC + "," + Constants.ADJECTIVE + "," + Constants.PRONOUN + "," + Constants.ADVERB + "|Same";
                    break;
                case SearchMode.Any:
                    pattern = Constants.NOUN + "," + Constants.NUMERIC + "," + Constants.ADJECTIVE + "," + Constants.PRONOUN + "," + Constants.VERB + "|Same";
                    break;
                //case SearchMode.AbsolutelyAny:
                //    pattern = Constants.NOUN + "," + Constants.NUMERIC + "," + Constants.ADJECTIVE + "," + Constants.PRONOUN + "," + Constants.VERB + Constants.ADVERB + "|Same";
                //    break;
                //case SearchMode.NotNarechie:
                //    pattern = "сущ-е, числ-е, прил-е, мест-е, глагол|Same";
                //    break;
                case SearchMode.NotNarechieGlagol:
                    pattern = Constants.NOUN +  "," + Constants.NUMERIC + "," + Constants.ADJECTIVE + "," + Constants.PRONOUN + "|Same";
                    break;
                case SearchMode.Prichastie:
                    pattern = Constants.VERB + "|" + Constants.PRICHASTIE;
                    break;
                case SearchMode.DeePrichastie:
                    pattern = Constants.VERB + "|" + Constants.DEEPRICHASTIE;
                    break;
                case SearchMode.Prilagatelnoe:
                    pattern = Constants.NOUN + "," + Constants.NUMERIC + "|" + Constants.ADJECTIVE;
                    break;
                case SearchMode.NotPril:
                    pattern = Constants.NOUN + "," + Constants.NUMERIC + "," + Constants.ADVERB + "," + Constants.PRONOUN + "," + Constants.VERB + "|Same";
                    break;
                case SearchMode.SuschGlagol:
                    pattern = Constants.NOUN + "," + Constants.VERB + "|Same";
                    break;
                case SearchMode.Chislitelnoe:
                    pattern = Constants.NUMERIC + "|Same";
                    break;
                case SearchMode.deenoun:
                    pattern = Constants.NOUN + "," + Constants.NUMERIC + "," + Constants.ADJECTIVE + "," + Constants.PRONOUN + "," + Constants.VERB + "," + Constants.ADVERB + "|" + Constants.DEENOUN;
                    break;
                default:
                    break;
            }
            return pattern;
        }
        //обрезка "полных" аффиксов
        public static string CutItOut(string AffUnit, string chast)
        {
            string[] affunit = AffUnit.Split(new char[] { '|' }, StringSplitOptions.RemoveEmptyEntries);
            string finAffUnit = "";
            string separ = "|";
            for (int i = 0; i < affunit.Length; i++)
            {
                switch (affunit[i])
                {
                    case "ҫӑм":
                    case "ҫӗм":
                        finAffUnit += affunit[i][0] + separ + affunit[i].Substring(1, 2) + separ; 
                        break;
                    case "ятна": 
                    case "атна": 
                    case "етне":
                        finAffUnit += affunit[i].Substring(0, 2) + separ + affunit[i].Substring(2, 2) + separ;
                        break;
                    case "масӑр":
                    case "месӗр":
                        finAffUnit += affunit[i].Substring(0, 2) + separ + affunit[i].Substring(2, 3) + separ;
                        break;
                    case "рӑм":
                    case "рӗм":
                    case "рӑн":
                    case "рӗн":
                    case "тӑм":
                    case "тӗм":
                    case "ман":
                    case "мен":
                    case "мӑп":
                    case "мӗп":
                    case "мап":
                    case "меп":
                    case "маҫ":
                    case "меҫ":
                    case "тӑп":
                    case "тӗп":
                    case "тӑн":
                    case "тӗн":
                    case "рӑр":
                    case "рӗр":
                        finAffUnit += affunit[i][0] + separ + affunit[i].Substring(1, 2) + separ;
                        break;
                    case "аттӑм":
                    case "еттӗм":
                    case "ӑттӑм":
                    case "ӗттӗм":
                        finAffUnit += affunit[i].Substring(0, 3) + separ + affunit[i].Substring(3, 2) + separ;
                        break;
                    case "ттӑм":
                    case "ттӗм":
                    case "ттӑн":
                    case "ттӗн":
                    case "атӑп":
                    case "етӗп":
                    case "атӑн":
                    case "етӗн":
                        finAffUnit += affunit[i].Substring(0, 2) + separ + affunit[i].Substring(2, 2) + separ;
                        break;
                    case "атчӗҫ":
                    case "етчӗҫ":
                        finAffUnit += affunit[i].Substring(0, 3) + separ + affunit[i].Substring(3, 1) + separ + affunit[i].Substring(4, 1) + separ;
                        break;
                    case "тчӗҫ":
                        finAffUnit += affunit[i].Substring(0, 2) + separ + affunit[i].Substring(2, 1) + separ + affunit[i].Substring(3, 1) + separ;
                        break;
                    case "аканни":
                    case "екенни":
                    case "яканни":
                        finAffUnit += affunit[i].Substring(0, 5) + separ + affunit[i].Substring(5, 1) + separ;
                        break;
                    case "манни":
                    case "менни":
                        finAffUnit += affunit[i][0] + separ + affunit[i].Substring(1, 3) + separ + affunit[i][4] + separ;
                        break;
                    case "асси":
                    case "есси":
                    case "ясси":
                        finAffUnit += affunit[i].Substring(0, 3) + separ + affunit[i][3] + separ;
                        break;
                    case "ни":
                        finAffUnit += "н" + separ + "и" + separ;
                        break;
                    case "малли":
                    case "мелли":
                        finAffUnit += affunit[i].Substring(0, 4) + separ + "и" + separ;
                        break;
                    case "нӑҫем":
                    case "нӗҫем":
                        finAffUnit += affunit[i].Substring(0, 2) + separ + affunit[i].Substring(2, 3) + separ;
                        break;
                    case "пӑр":
                    case "пӗр":
                        finAffUnit += affunit[i][0] + separ + affunit[i].Substring(1, 2) + separ;
                        break;
                    case "сене":
                        finAffUnit += "сем" + separ + "е" + separ;
                        break;
                    case "аҫҫӗ":
                    case "еҫҫӗ":
                        finAffUnit += affunit[i].Substring(0,2) + separ + "ҫ" + separ + "ӗ" + separ;
                        break;
                    case "мест":
                        finAffUnit += "мес" + separ + "т" + separ;
                        break;
                    case "масть":
                        finAffUnit += "мас" + separ + "ть" + separ;
                        break;
                    case "рӗ":
                        finAffUnit += affunit[i][0] + separ + "ӗ" + separ;
                        break;
                    case "рӗҫ":
                    case "чӗҫ":
                        finAffUnit += affunit[i][0] + separ + "ӗ" + separ + "ҫ" + separ;
                        break;
                    case "мӗ":
                        finAffUnit += "м" + separ + "ӗ" + separ;
                        break;
                    case "ӗҫ":
                        finAffUnit += "ӗ" + separ + "ҫ" + separ;
                        break;
                    case "атчӗ":
                    case "етчӗ":
                        finAffUnit += affunit[i][0] + "тч" + separ + "ӗ" + separ;
                        break;
                    case "чӗ":
                        if (chast == "глагол" && affunit[0] =="чӗ") finAffUnit += "ч" + separ + "ӗ" + separ;
                        else finAffUnit += affunit[i] + separ;
                        break;
                    default:
                        finAffUnit += affunit[i] + separ;
                        break;
                }
            }
            finAffUnit = finAffUnit.Remove(finAffUnit.Length - 1);
            return finAffUnit;
        }
    }
}
