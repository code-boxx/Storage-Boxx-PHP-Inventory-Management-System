# (A) LOAD REQUIRED MODULES
import os, json, glob
from langchain.vectorstores import Chroma
from langchain.embeddings import GPT4AllEmbeddings
from langchain.text_splitter import RecursiveCharacterTextSplitter
from langchain.document_loaders import (
  CSVLoader,
  PyMuPDFLoader,
  TextLoader,
  UnstructuredEPubLoader,
  UnstructuredHTMLLoader,
  UnstructuredMarkdownLoader,
  UnstructuredODTLoader,
  UnstructuredPowerPointLoader,
  UnstructuredWordDocumentLoader
)

# (B) FLAGS & SETTINGS
# (B1) PATH
set = { "path" : os.path.dirname(os.path.realpath(__file__)) }
with open(os.path.join(set["path"], "settings.json"), "r") as fs:
  set.update(json.load(fs))
  fs.close()
set["path_db"] = os.path.join(set["path"], "db")
set["path_docs"] = os.path.join(set["path"], "docs")
if os.path.exists(set["path_db"]):
  print("Database already exist - " + set["path_db"])
  exit()

# (B2) MAP FILE TYPES TO LOADER
set["map"] = {
  ".csv" : CSVLoader,
  ".pdf" : PyMuPDFLoader,
  ".txt" : TextLoader,
  ".epub" : UnstructuredEPubLoader,
  ".html" : UnstructuredHTMLLoader,
  ".md" : UnstructuredMarkdownLoader,
  ".odt" : UnstructuredODTLoader,
  ".doc" : UnstructuredWordDocumentLoader, ".docx" : UnstructuredWordDocumentLoader,
  ".ppt": UnstructuredPowerPointLoader, ".pptx": UnstructuredPowerPointLoader
}

# (B3) GET DOCUMENTS TO IMPORT
all = []
for ext in set["map"]:
  all.extend(glob.glob(os.path.join(set["path_docs"], "*" + ext), recursive=True))
if (len(all) == 0):
  print("No documents to import in " + set["path_docs"])
  exit()

# (C) IMPORT PROCESS
# (C1) CREATE EMPTY-ISH DATABASE
print("Creating database")
db = Chroma.from_texts (
  texts = ["Code Boxx rocks!"],
  embedding = GPT4AllEmbeddings(),
  persist_directory = set["path_db"]
)
db.persist()

# (C2) ADD DOCUMENTS
splitter = RecursiveCharacterTextSplitter(
  chunk_size = set["doc_chunks"], chunk_overlap = set["doc_overlap"]
)
for doc in all:
  print("Adding - " + doc)
  name, ext = os.path.splitext(doc)
  db.add_documents(splitter.split_documents(set["map"][ext](doc).load()))
db.persist()
db = None