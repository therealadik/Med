// Для работы с сетью
import java.net.*;
// Для работы с потоками ввода-вывода
import java.io.*;
// Для вывода диалогового окна
import javax.swing.JOptionPane;

import java.awt.BorderLayout;
import java.awt.EventQueue;

import javax.swing.JFrame;
import javax.swing.JPanel;
import javax.swing.border.EmptyBorder;
import javax.swing.JTable;
import javax.swing.JScrollPane;
import javax.swing.table.DefaultTableModel;
import java.awt.Font;
import java.awt.Color;
import javax.swing.JButton;
import java.awt.event.ActionListener;
import java.awt.event.ActionEvent;


public class prog extends JFrame {

	private JPanel contentPane;
	private JTable tableTovar;
	
	// Метод для создания GET-запроса
	private String makeGet()
	{
		// Переменная для формирования GET-запроса
	    String rez = "?";
	    
	    // Перебираем десять строчек таблицы в цикле
	    for (int i=0;i<10;i++)
	    {	
	       // Прибавляем в конец строки значение: tov{номер}=
	       rez += ("tov"+(i+1)+"=");
	       
	       // Переменная для количества 
		   int kol;
	    	
	       try // Попытка
	       {
		     // Получаем значение из ячейки таблицы
	    	 // приводим к типу String и срезаем пробелы справа и слева 
	         String str = tableTovar.getValueAt(i, 3).toString().trim();
	         // Приводим к целому типу
	         kol = Integer.parseInt(str);
	       }
	       catch (Exception e) // Исключение
	       {
	    	 // Если в ячейке ничего не было - считаем, что нулевое значение
	    	 kol=0;
	       }
	       
	       // Прибавляем в конец строки количество и символ: &
	       rez += ""+kol+"&";	       	       
	    }
	    
	    // Возврат значения
	    return rez;
	}

	/**
	 * Launch the application.
	 */
	public static void main(String[] args) {
		EventQueue.invokeLater(new Runnable() {
			public void run() {
				try {
					prog frame = new prog();
					frame.setVisible(true);
				} catch (Exception e) {
					e.printStackTrace();
				}
			}
		});
	}

	/**
	 * Create the frame.
	 */
	public prog() {
		setResizable(false);
		setTitle("Поступление товара");
		setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
		setBounds(100, 100, 578, 292);
		contentPane = new JPanel();
		contentPane.setBorder(new EmptyBorder(5, 5, 5, 5));
		setContentPane(contentPane);
		contentPane.setLayout(null);
		
		JScrollPane scrollPane = new JScrollPane();
		scrollPane.setBounds(10, 11, 525, 185);
		contentPane.add(scrollPane);
		
		tableTovar = new JTable();
		tableTovar.setForeground(new Color(0, 0, 128));
		tableTovar.setFont(new Font("Tahoma", Font.PLAIN, 11));
		scrollPane.setViewportView(tableTovar);
		tableTovar.setModel(new DefaultTableModel(
			new Object[][] {
				{"1", "Кароль", "", null},
				{"2", "Прополис", null, null},
				{"3", "Воск пчелиный", null, null},
				{"4", "Цветочная пыльца", null, null},
				{"5", "Перга", null, null},
				{"6", "Маточное молочко", null, null},
				{"7", "Трутневое молочко", null, null},
				{"8", "Пчелиный яд", null, null},
				{"9", "Пчелиная огнёвка", null, null},
				{"10", "Пчелиный подмор", null, null},
			},
			new String[] {
				"№", "Наименование товара", "Остаток на складе", "Количество поступления"
			}
		) {
			final Class[] columnTypes = new Class[] {
				Object.class, Object.class, Object.class, Integer.class
			};
			public Class getColumnClass(int columnIndex) {
				return columnTypes[columnIndex];
			}
			final boolean[] columnEditables = new boolean[] {
				false, false, false, true
			};
			public boolean isCellEditable(int row, int column) {
				return columnEditables[column];
			}
		});
		tableTovar.getColumnModel().getColumn(0).setResizable(false);
		tableTovar.getColumnModel().getColumn(0).setPreferredWidth(36);
		tableTovar.getColumnModel().getColumn(1).setResizable(false);
		tableTovar.getColumnModel().getColumn(1).setPreferredWidth(211);
		tableTovar.getColumnModel().getColumn(2).setResizable(false);
		tableTovar.getColumnModel().getColumn(2).setPreferredWidth(127);
		tableTovar.getColumnModel().getColumn(3).setResizable(false);
		tableTovar.getColumnModel().getColumn(3).setPreferredWidth(150);
		
		JButton buttonOK = new JButton("Выполнить");
		buttonOK.addActionListener(new ActionListener() {
			public void actionPerformed(ActionEvent arg0) {

				// Формируем GET-запрос для отправки
				String str = "http://puh/tovar.php"+makeGet();
							
				// Переменная для проверки успешной отправки данных
				boolean flag = false;
							
				try
				{					
				   // Адрес подключения
				   URL url = new URL(str);
			         // HTTP-подключение
				   HttpURLConnection conn = 
					           (HttpURLConnection)url.openConnection();
			         // Подключаемся
				   conn.connect();
					            
			         // Поток чтения данных
			         BufferedReader in = new BufferedReader(
					     new InputStreamReader(conn.getInputStream()));
					            
			         // Получаем строку-ответ
				   String inputLine = in.readLine().trim();
				                	                
				                
				   // Проверяем ответную строку
				   if (inputLine.indexOf('~')>=0)
				   {
			             // Меняем флаг на признак успешной передачи
				       flag=true;
			             // Получаем массив значений
				       String[] mas = inputLine.split("~");
			             // Перебираем значения в цикле
				       for (int i=0;i<mas.length;i++)
				       {
			                // Помещаем значения в таблицу
				          tableTovar.setValueAt(mas[i], i, 2);
				       }
				   }
				                
				   // Закрываем поток
				   in.close();		  
				   // Отключаемся
			         conn.disconnect();		            
			          conn=null;
				  } 
				  catch (Exception ignored) {}
							
				  // Если отправка данных не удалась
				  if (!flag)
				  {
					JOptionPane.showMessageDialog(
						null, 
					      "Возможно интернет не подключен.",
						"Ошибка отправки данных!", 0);
				  }				
				
			}
		});
		buttonOK.setForeground(new Color(0, 128, 0));
		buttonOK.setFont(new Font("Tahoma", Font.PLAIN, 17));
		buttonOK.setBounds(166, 212, 190, 31);
		contentPane.add(buttonOK);
	}
}



