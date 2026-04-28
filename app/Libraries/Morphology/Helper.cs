using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Diagnostics;

namespace MorfParsingLibrary
{
    class Helper
    {
        //def values
        #region vars
        public static char[] separator = { ' ', ',', '.', ':', '\t', '\n', '?', '!', '—', '"', '«', '»', '…', ';' }; //разделители слов
        static List<int> l;

        static string[] separator_EndOfWord = { "-и", "-ши", "-ҫке", "-ха", "-им", "-шим", "-а", "-е", "-иҫ", "-мӗн", "-тӑк", "-тӗк", "-тӑр", "-тӗр", "-ах", "-ех" };//окончания слов
        static string[] letters = { "а", "ӑ", "б", "в", "г", "д", "е", "ё", "ӗ", "ж", "з", "и", "й", "к", "л", "м", "н", "о", "п", "р", "с", "ҫ", "т", "у", "ÿ", "ф", "х", "ц", "ч", "ш", "щ", "ы", "э", "ю", "я" };

        public static string[] defaults = { "noun," + Constants.NOUN + "," + Constants.ED_CHISLO + "," + Constants.NULL + "," + Constants.OSN_P + "," + Constants.FACE1 + "," + Constants.POSITIVE + "," + Constants.NULL,
                                            "verb," + Constants.VERB + "," + Constants.ED_CHISLO + "," + Constants.NAST_V + "," + Constants.NULL + "," + Constants.FACE2 + "," + Constants.POSITIVE + "," + Constants.NOTINF,
                                            "adj," + Constants.ADJECTIVE + "," + Constants.ED_CHISLO + "," + Constants.NULL + "," + Constants.OSN_P + "," + Constants.NULL + "," + Constants.POSITIVE + "," + Constants.NULL,
                                            "adv," + Constants.ADVERB + "," + Constants.ED_CHISLO + "," + Constants.NULL + "," + Constants.NULL + "," + Constants.NULL + "," + Constants.POSITIVE + "," + Constants.NULL,
                                            "pron," + Constants.PRONOUN + "," + Constants.ED_CHISLO + "," + Constants.NULL + "," + Constants.OSN_P + "," + Constants.FACE3 + "," + Constants.POSITIVE + "," + Constants.NULL 
                                          };

        public static string[] pron_defaults = { "эпӗ," + Constants.ED_CHISLO + "," + Constants.OSN_P + "," + Constants.FACE1,
                                                 "эп," + Constants.ED_CHISLO + "," + Constants.OSN_P + "," + Constants.FACE1,
                                                 "эсӗ," + Constants.ED_CHISLO + "," + Constants.OSN_P + "," + Constants.FACE2,
                                                 "эс," + Constants.ED_CHISLO + "," + Constants.OSN_P + "," + Constants.FACE2,
                                                 "вӑл," + Constants.ED_CHISLO + "," + Constants.OSN_P + "," + Constants.FACE3,
                                                 "эпир," + Constants.MN_CHISLO + "," + Constants.OSN_P + "," + Constants.FACE1,
                                                 "эсир," + Constants.MN_CHISLO + "," + Constants.OSN_P + "," + Constants.FACE2,
                                                 "вӗсем," + Constants.MN_CHISLO + "," + Constants.OSN_P + "," + Constants.FACE3,
                                                 "хам," + Constants.ED_CHISLO + "," + Constants.OSN_P + "," + Constants.FACE1,
                                                 "ху," + Constants.ED_CHISLO + "," + Constants.OSN_P + "," + Constants.FACE2,
                                                 "хӑй," + Constants.ED_CHISLO + "," + Constants.OSN_P + "," + Constants.FACE3,
                                                 "хамӑр," + Constants.MN_CHISLO + "," + Constants.OSN_P + "," + Constants.FACE1,
                                                 "хӑвӑр," + Constants.MN_CHISLO + "," + Constants.OSN_P + "," + Constants.FACE2,
                                                 "хӑв," + Constants.ED_CHISLO + "," + Constants.OSN_P + "," + Constants.FACE2,
                                                 "пирӗн," + Constants.MN_CHISLO + "," + Constants.ROD_P + "," + Constants.FACE1,
                                                 "сирӗн," + Constants.MN_CHISLO + "," + Constants.ROD_P + "," + Constants.FACE2,
                                                 "вӗсен," + Constants.MN_CHISLO + "," + Constants.ROD_P + "," + Constants.FACE3,
                                                 "ман," + Constants.ED_CHISLO + "," + Constants.ROD_P + "," + Constants.FACE1,
                                                 "сан," + Constants.ED_CHISLO + "," + Constants.ROD_P + "," + Constants.FACE2,
                                                 "ун," + Constants.ED_CHISLO + "," + Constants.ROD_P + "," + Constants.FACE3
                                               };


        static string[] onlyXI =  { "ӗмӗр", "паян", "ӗнер", "ыран", "хӗлле", "ҫулла", "кӗркунне", "ҫуркунне", "хупах" };
        static char[] onlyRI = { 'л', 'н', 'д', 'т', 'ь' };
        static char[] GLAS = { 'а', 'е', 'ӑ', 'ӗ', 'и' };
        static string[] SayMyName = { "кил", "тул" };
        static string[] AffUnit; 
        #endregion
        //конверт корней
        public static string convertRoot(string root, string chastrechi)
        {

            if (chastrechi == Constants.VERB)
            {
                switch (root)
                {
                    case "шӑв":
                    case "тӑв":
                    case "сӑв":
                    case "ҫӑв":
                        return root[0] + "у";
                    case "сӗв":
                        return "сÿ";
                }
            }
            if (chastrechi == Constants.NOUN)
            {
                switch (root)
                {
                    case "тӑв":
                    case "ҫӑв":
                        return root[0] + "у";
                }
            }
            if (root == "вӗсен") return "вӗсем";
            
            return root;
            
        }
        //конверт частей речи со словаря
        public static string convertPartOfSpeech(string p)
        {
            switch (p)
            {
                case "noun":
                    return Constants.NOUN;
                case "verb":
                    return Constants.VERB;
                case "adj":
                    return Constants.ADJECTIVE;
                case "pron":
                    return Constants.PRONOUN;
                case "num":
                    return Constants.NUMERIC;
                case "adv":
                    return Constants.ADVERB;
                case "part":
                    return Constants.PART;
                case "conj":
                    return Constants.CONJ;

                case "прил-е":
                    return Constants.ADJECTIVE;
                case "сущ-е":
                    return Constants.NOUN;
                case "числ-е":
                    return Constants.NUMERIC;
                case "мест-е":
                    return Constants.PRONOUN;
                case "глагол":
                    return Constants.VERB;
                default:
                    return p;
            }
        }
        //отбрасываем возможные окончания
        public static string loseEnds(string word, out string aff)
        {
            aff = "";
            bool flag1 = false;
            bool flag2 = false;

            while (!flag1)
            {
                for (int i = 0; i < separator_EndOfWord.Length; i++)
                {
                    if (word.EndsWith(separator_EndOfWord[i]))
                    {
                        word = word.Substring(0, word.Length - separator_EndOfWord[i].Length);
                        aff = "|" + separator_EndOfWord[i] + aff;
                        flag2 = true;
                        break;
                    }
                    flag2 = false;
                }
                if (!flag2) flag1 = true;
            }
            
            return word;
        }
        //для конечного определения части речи
        public static string ChastRechiSolver(string chastrechi, string pattern)
        {
            string ex = pattern.Split(new char[] { '|' }, StringSplitOptions.RemoveEmptyEntries)[1];
            string res;
            switch (ex)
            {
                case "Same": res = chastrechi;
                    break;
                case Constants.DEENOUN:
                    res = (chastrechi == Constants.VERB) ? Constants.DEEPRICHASTIE : chastrechi;
                    break;
                default: res = ex;
                    break;
            }
            return res;
        }
        //получение номеров строк для каждого символа алфавита
        public static void GetNumbersOfLines(string [] dict)
        {
            l = new List<int>();
            int k = 0;
            for (int i = 0; i < dict.Length; i++)
            {
                for (int j = 0; j < letters.Length; j++)
                {
                    if  ((k < letters.Length)&& (dict[i].StartsWith(letters[k])))
                    {
                        l.Add(i);
                        k += 1;
                        break;
                    }
                }
            }
        }
        //получение нужной пары номеров строк в словаре
        public static void getCurrentNumbers(string word, out int begin, out int end)
        {
            begin = end = 0;
            for (int i = 0; i < letters.Length; i++)
            {
                if (word.StartsWith(letters[i]))
                {
                    if (i < l.Count - 1)
                    {
                        begin = l[i];
                        end = l[i + 1];
                    }
                    else
                    {
                        begin = l[i];
                        end = MorfParser.Dictionary.Length;
                    }
                    
                    break;
                }
            }
        }
        //-ай -яй -ей обработка
        public static string TransformSomeWords(string word)
        {
            if (word.Length < 3) return word;
            string[] excepts = { "паян", "чее", "япала"};
            if (excepts.Any(el => word.Contains(el))) return word;
            //1
            string[] a = { "ӑяя", "аяя", "уя", "ӑя", "ая", "ее", "яя", "яй" };
            string[] b = { "ӑйайа", "айайа", "уйа", "ӑйа", "айа", "ейе", "айа", "ай" };

            for (int i = 0; i < a.Length; i++)
			{
			    if (word.IndexOf(a[i], 1, word.Length - 2) != -1)
                {
                    word = word.Replace(a[i], b[i]);
                    return word;
                }
			}

            if (word.Contains("ятт")) return word;
            //2
            string[] c = { "ят", "яп", "яп", "яҫ" };
            if (c.Any(el => word.Contains(el))) word = word.Replace("я", "яа");
            //3
            string[] d = { "кал", "кел"};
            for (int i = 0; i < d.Length; i++)
            {
                if (word.IndexOf(d[i], 1, word.Length - 3) != -1)
                {
                    word = word.Replace(d[i], d[i] + d[i][1]);
                    break;
                }
            }


            return word;
            
        }
        //конечная проверка подходимости слова к аффиксу
        public static bool CheckCompatibility(string word, string AffUnit, string chastrechi)
        {

            bool result = true;

            string[] aff_mas = AffUnit.Split(new char[] { '|' }, StringSplitOptions.RemoveEmptyEntries);
            Helper.AffUnit = aff_mas;
            char word_lastsymbol = word[word.Length - 1]; //последний символ слова
            char aff_firstsymbol = aff_mas[0][0]; //первый символ первого аффикса
            string aff_first = aff_mas[0]; //первый аффикс

            bool sogl = ContextRules.Soglasnaya(word); //посл. буква соглас
            bool consistent = ContextRules.Consistency(word);//слово тверд/мягк
            bool zvonk = ContextRules.Zvonko(word_lastsymbol);//согл звонк/глухой
            


            //условия проверки слово и аффикс
            bool s1 = (sogl) && (word_lastsymbol == aff_firstsymbol) && (word_lastsymbol != 'н') && (word_lastsymbol != 'х');
            bool s2 = ((aff_first == "а") || (aff_first == "е")) && (aff_mas.Length > 1);
            bool s3 = (((aff_first == "а") || (aff_first == "е")) && (aff_mas.Length > 1) && (aff_mas[1]) == "ҫ");
            bool s4 = ((aff_first == "а") || (aff_first == "е")) && (aff_mas.Length > 1) && (aff_mas[1] == "тпӑр" || aff_mas[1] == "тпӗр");
            bool s5 = (aff_mas.Length > 1) && ((aff_first == "а") || (aff_first == "е")) && ((aff_mas[1] == "ма") || (aff_mas[1] == "ме"));//урама!=ур+а+ма
            //
            bool s6 = (chastrechi == Constants.NOUN || chastrechi == Constants.ADJECTIVE || chastrechi == Constants.NUMERIC) && ((!sogl || zvonk) && (aff_first == "чӗ") || sogl && !zvonk && (aff_first == "ччӗ")); //ччӗ/чӗ для сущ/прил
            bool s7 = (chastrechi == Constants.NOUN || chastrechi == Constants.ADJECTIVE || chastrechi == Constants.ADJECTIVE) && ((!sogl || zvonk) && (aff_first == "чен") || sogl && !zvonk && (aff_first == "ччен"));//ччен/чен для сущ/прил
            bool s8 = (sogl && (aff_first == "ллӑ" || aff_first == "ллӗ" || aff_first == "лли")) || (!sogl && (aff_first == "лӑ" || aff_first == "лӗ" || aff_first == "ли"));//лӑ/лӗ/ли ллӑ/ллӗ/лли
            bool s9 = (sogl && (aff_first == "лла" || aff_first == "лле")) || (!sogl && (aff_first == "ла" || aff_first == "ле"));//ла/ле лла/лле
            bool s10 = (aff_first == "хи") && !(onlyXI.Contains(word)) || ((aff_first == "ри") || (aff_first == "ти")) && (onlyXI.Contains(word));//хи/ри/ти
            bool s11 = ((aff_first == "хи") || (aff_first == "ти")) && (onlyRI.Contains(word_lastsymbol) && !(SayMyName.Contains(word))); //ри
            bool s12 = (aff_first == "шкал") && !(word_lastsymbol == 'о'); //-шкал только перед о;
            //
            bool s13 = (chastrechi == Constants.ADJECTIVE || chastrechi == Constants.NUMERIC || chastrechi == Constants.NOUN) && (GLAS.Contains(word_lastsymbol)) && (aff_first == "ӗн" || aff_first == "ӑн");//если прил/числ/сущ конч. на глас афф не -ӑн/ӗн
            bool s14 = (chastrechi == Constants.ADJECTIVE || chastrechi == Constants.NUMERIC || chastrechi == Constants.PRONOUN) && (aff_first == "ӗ");//в прил/числ не мб -ӗ первым

            bool s15 = (aff_first == "ах" || aff_first == "ех") && (word_lastsymbol == 'а' || word_lastsymbol == 'е'); //не дб а/е перед ах/ех
            //bool s16 = (aff_firstsymbol == 'т') && !sogl; //не дб гл. перед аффиксом на т
            bool s17 = (word_lastsymbol == 'т') && (aff_firstsymbol == 'ч') && (chastrechi == Constants.VERB);
            bool s18 = (chastrechi == Constants.ADVERB) && aff_mas.Contains("ӗ");


            //условия проверки аффикс и аффикс
            bool k01 = (aff_mas.Contains("мас") || aff_mas.Contains("мес")) && (aff_mas.Contains("тӑр") || aff_mas.Contains("тӗр"));
            bool k02 = (aff_mas.Contains("мас") || aff_mas.Contains("мес")) && (aff_mas.Contains("ӑр") || aff_mas.Contains("ӗр"));
            bool k03 = (aff_mas.Contains("ат") || aff_mas.Contains("ет")) && aff_mas.Contains("чӗ");
            bool k04 = (aff_mas.Contains("н") && aff_mas.Contains("ӗ"));
            bool k05 = (aff_mas.Contains("ӑн") && aff_mas.Contains("чӗ"));
            bool k06 = (aff_mas.Contains("кал") || aff_mas.Contains("кел")) && (aff_mas.Contains("ат") || aff_mas.Contains("ет"));
            bool k07 = (aff_mas.Contains("ар") || aff_mas.Contains("ер")) && (aff_mas.Contains("ат") || aff_mas.Contains("ет"));
            bool k08 = (aff_mas.Contains("ас") || aff_mas.Contains("ес")) && (aff_mas.Contains("ан") || aff_mas.Contains("ен"));
            bool k09 = aff_mas.Contains("н") && (aff_mas.Contains("чи") || aff_mas.Contains("чӗ"));



            bool temp = s1 || s2 || s3 || s4 || s5 || s6 || s7 || s8 || s9 || s10 || s11 || s12 || s13 || s14 || s15 || s17 || s18;
            bool temp1 = k01 || k02 || k03 || k04 || k05  || k07 || k08 || k09;

            if (temp || temp1) result = false;

            Trace.WriteLineIf(s1,"s1 " + s1);
            Trace.WriteLineIf(s2, "s2 " + s2);
            Trace.WriteLineIf(s3, "s3 " + s3);
            Trace.WriteLineIf(s4, "s4 " + s4);
            Trace.WriteLineIf(s5, "s5 " + s5);
            Trace.WriteLineIf(s6, "s6 " + s6);
            Trace.WriteLineIf(s7, "s7 " + s7);
            Trace.WriteLineIf(s8, "s8 " + s8);
            Trace.WriteLineIf(s9, "s9 " + s9);
            Trace.WriteLineIf(s10, "s10 " + s10);
            Trace.WriteLineIf(s11, "s11 " + s11);
            Trace.WriteLineIf(s12, "s12 " + s12);
            Trace.WriteLineIf(s13, "s13 " + s13);
            Trace.WriteLineIf(s14, "s14 " + s14);
            Trace.WriteLineIf(s15, "s15 " + s15);
            //Trace.WriteLineIf(s16, "s16 " + s16);
            Trace.WriteLineIf(s17, "s17 " + s17);
            Trace.WriteLineIf(s18, "s17 " + s18);



            Trace.WriteLine("Совпадение найдено. \n Слово совместимо с аффиксом? - " + result);
            
            return result;
        }
        //проверка нахождения 2х аффиксов рядом в аффюните
        public static bool Adjust(string laff, string raff)
        {
            int lpos = Array.IndexOf(Helper.AffUnit, laff);
            int rpos = Array.IndexOf(Helper.AffUnit, raff);

            if (lpos == -1 || rpos == -1) return false;
            else if (Math.Abs(lpos - rpos) == 1) return true;
            else return false;
        }
    }
}
