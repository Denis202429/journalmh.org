using System;
using System.Collections.Generic;
using System.IO;
using System.Text.RegularExpressions;
using System.Diagnostics;


namespace MorfParsingLibrary
{
    class ImprovedAffixHandler
    {
        //объекты для хранения частей речи и корней с аффиксами
        List<string> ChastRechiCollection;
        MorfParser morfParser;

        //номера строк в словаре
        int begin, end;

        //начальная инициализация[конструктор]
        public ImprovedAffixHandler(List<string> l, MorfParser mp, int begin, int end)
        {
            this.begin = begin;
            this.end = end;
            ChastRechiCollection = l;
            morfParser = mp;
        }
        //***********************************************************************************//
        ///ГЛАВНАЯ РАБОТА
        //Основной метод разделения слов на корни и аффиксы, также для определения части речи [Рекурсивный]
        public void MainWordDivider(string word, string AffUnit)
        {
            for (int i = 1; i < word.Length; i++)
            {
                
                string aff = word.Substring(word.Length - i);
                if (AffixInfoProvider.AffixesSuitable(aff, AffUnit))
                {
                    FindInDictionary(word.Substring(0, word.Length - aff.Length), AffixInfoProvider.AddAffixToUnit(aff, AffUnit));
                    Trace.WriteLine("==================================================");
                    MainWordDivider(word.Substring(0, word.Length - aff.Length), AffixInfoProvider.AddAffixToUnit(aff, AffUnit));
                }
            }
        }

        //***********************************************************************************//
        //Поиск слова в основном словаре
        private void FindInDictionary(string word, string AffUnit)
        {
            
            Trace.WriteLine("Анализируем: " + word + " " + AffUnit);
            
            string pattern = AffixInfoProvider.PatternPicker(AffUnit);

            string[] default_syllables = ContextRules.DefaultSyllables(AffUnit);
            string[] context_syllables = null; string context_chastrechi = null;
            string[] syll;
            string t_word = ""; string t_AffUnit = "";
            ContextRules.FindTemplate(word, AffUnit, MorfParser.Rules, ref context_syllables, ref context_chastrechi);

            for (int i = begin; i < end; i++)            
            {
                string[] seek_line = MorfParser.Dictionary[i].Split(new char[] { ',' });
                string chastrechi = Helper.convertPartOfSpeech(seek_line[1]);
                Match match = Regex.Match(pattern, chastrechi);

                if (match.Success)
                {
                     
                    if (ContextRules.CheckChastRechi(chastrechi, context_chastrechi))
                    {
                        syll = context_syllables;
                        t_word = ContextRules.WORD;
                        t_AffUnit = ContextRules.AFFUNIT;
                    }
                    else
                    {
                        syll = default_syllables;
                        t_word = Helper.convertRoot(word, chastrechi);
                        t_AffUnit = AffUnit;
                    }

                    for (int j = 0; j < syll.Length; j++)
                    {
                        string fin_word = t_word + syll[j];
                        
                        if (fin_word == seek_line[0] && Helper.CheckCompatibility(fin_word, t_AffUnit, chastrechi))
                        {
                            string chast = Helper.ChastRechiSolver(chastrechi, pattern);
                            string fin_aff = AffixInfoProvider.CutItOut(t_AffUnit, chast);

                            ChastRechiCollection.Add(chast);
                            morfParser.AddtoRootAffixCollection(fin_word, fin_aff);

                            Trace.WriteLine("Добавляем: [" + chast + ": " + fin_word + " " + fin_aff + "]");
                        }
                    }
                }
            }
            CheckExceptions(word, AffUnit);
            Trace.WriteLine("Переход к следующей паре.");
        }
        //Необработанные исключения
        private void CheckExceptions(string word, string AffUnit)
        {
            string aff_first = AffUnit.Split(new char[] {'|'}, StringSplitOptions.RemoveEmptyEntries)[0];
            string chastrechi = "";
            string exception_word = word; string exception_AffUnit = AffUnit;

            foreach (string exception_row in MorfParser.Exceptions)
            {
                string[] exception_row_array = exception_row.Split(new char[]{' '}, StringSplitOptions.RemoveEmptyEntries);
                if ((exception_row_array[0] == word) && (exception_row_array[1] == aff_first))
                {
                    exception_word = exception_row_array[2];
                    chastrechi = exception_row_array[3];

                    ChastRechiCollection.Add(Helper.convertPartOfSpeech(chastrechi));
                    morfParser.AddtoRootAffixCollection(exception_word, exception_AffUnit);

                    Trace.WriteLine("Найдено исключение: [" + exception_word + " " + exception_AffUnit + " " + chastrechi + "]");
                }
            }
        }


    }
}
