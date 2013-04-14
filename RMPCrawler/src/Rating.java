class Rating
{

    String university = "";
    String professor = "";
    String overallProfQual = "";
    String goodPoorAvgQual = "";
    String department = "";
    private String courseNum = "";
    String easiness = "NULL";
    String helpfulness = "NULL";
    String clarity = "NULL";
    String raterInterest = "NULL";
    String rootJSPTid = "";
    String timeStamp = "";
    String comment = "";
    
    public Rating(String profName, String profUniversity, String profDepartment, String profQuality, String tid)
    {
    	professor = profName;
    	university = profUniversity;
    	department = profDepartment;
    	overallProfQual = profQuality;
    	rootJSPTid = tid;
    }
    
    public String toString()
    {
        String toReturn = "";

        toReturn += university + "\t";
        toReturn += professor + "\t";
        toReturn += overallProfQual + "\t";
        
        if(goodPoorAvgQual.toLowerCase().equals("good quality"))
        {
        	toReturn += "2\t";
        }else if(goodPoorAvgQual.toLowerCase().equals("poor quality")){
        	toReturn += "0\t";
        }else if(goodPoorAvgQual.toLowerCase().equals("average quality")){
        	toReturn += "1\t";
        }else{
        	toReturn += "NULL\t";
        }

        toReturn += department + "\t";
        toReturn += courseNum + "\t";
        toReturn += easiness + "\t";
        toReturn += helpfulness + "\t";
        toReturn += clarity + "\t";
        toReturn += raterInterest + "\t";
        toReturn += rootJSPTid + "\t";
        toReturn += timeStamp + "\t";
        toReturn += comment + "\r\n";
        
        return toReturn;
    }

	public void setCourseNum(String stringCourse) {
		int intCourse = 0;
		try{
			intCourse = Integer.parseInt(stringCourse);
		}catch(Exception e){
			return;
		}
		
		if(intCourse>=100 && intCourse<=999)
		{
			courseNum  = Integer.toString(intCourse);	
		}
	}
}